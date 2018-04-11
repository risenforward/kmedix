<?php

namespace App\Http\Controllers\Api\Customer;

use App\Device;
use App\Jobs\SendPushNotifications;
use App\Rating;
use App\ServiceRequest;
use App\ServiceRequestPhoto;
use App\User;
use Carbon\Carbon;
use DebugBar\DebugBar;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class ServiceRequestController extends Controller
{
    use \ApiResponseTrait, \ImageTrait;

    public function create($id, Request $request)
    {
        $response = $this->response;

        try {
            $data = $request->all();

            $device = Device::find($id)->load('deviceModel');

            $serviceRequests = ServiceRequest::where(function ($query) use ($device, $data) {
                $query->where('device_id', $device->id);
                $query->where('type', $data['type']);
                $query->where('status', '<>', ServiceRequest::CLOSED);
            })->get();

            if (!$serviceRequests->isEmpty()) {
                $response['meta'] = $this->makeErrorMeta(409, 'Already exist open service request for this device.');
                $serviceRequest = $serviceRequests->first();
                $response['data'] = [
                    'id' => $serviceRequest->id,
                    'type' => $serviceRequest->type,
                    'type_name' => ServiceRequest::$types[$serviceRequest->type],
                    'device' => [
                        'id' => $serviceRequest->device_id,
                        'serial_number' => $serviceRequest->device->serial_number,
                        'model' => $serviceRequest->device->deviceModel->name,
                    ]
                ];
            } else {
                $consumable = false;
                if ($device->extended_warranty) {
                    $consumable = Carbon::parse($device->extended_warranty_start)->addMonth($device->extended_warranty)->format(DEFAULT_DATE_FORMAT);
                } else if($device->consumable_warranty) {
                    $consumable = Carbon::parse($device->install_date)->addMonth($device->consumable_warranty)->format(DEFAULT_DATE_FORMAT);
                }

                $response['data'] = [
                    'id' => $device->id,
                    'serial_number' => $device->serial_number,
                    'model' => $device->deviceModel->name,
                    'install_date' => $device->getFInstallDate(),
                    'warranty_end_at' => Carbon::parse($device->install_date)->addMonth($device->warranty)->format(DEFAULT_DATE_FORMAT),
                    'consumable_warranty_end_at' => $consumable,
                    'image_url' => $device->deviceModel->photo_url,
                ];
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function store($id, Request $request)
    {
        $response = $this->response;

        $validator = \Validator::make($request->all(), [
            'type' => 'required',
            'description' => 'required',
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $data = $request->all();

                $device = Device::find($id);

          $serviceRequests = ServiceRequest::where(function ($query) use ($device, $data) {
                $query->where('device_id', $device->id);
                $query->where('type', $data['type']);
                $query->where('status', '<>', ServiceRequest::CLOSED);
            })->get();

            if (!$serviceRequests->isEmpty()) {
                $response['meta'] = $this->makeErrorMeta(409, 'Already exist open service request for this device.');
                $serviceRequest = $serviceRequests->first();
                $response['data'] = [
                    'id' => $serviceRequest->id,
                    'type' => $serviceRequest->type,
                    'type_name' => ServiceRequest::$types[$serviceRequest->type],
                    'device' => [
                        'id' => $serviceRequest->device_id,
                        'serial_number' => $serviceRequest->device->serial_number,
                        'model' => $serviceRequest->device->deviceModel->name,
                    ]
                ];
            }
			else {
                $serviceRequest = new ServiceRequest([
                    'type' => $data['type'],
                    'request_date' => Carbon::now(),
                    'description' => $data['description'],
                    'status' => ServiceRequest::REQUESTED,
                ]);

                $photos = null;
                if (isset($data['images'])) {
                    $photos = ServiceRequestPhoto::whereIn('id', $data['images'])->get();
                }

                \DB::transaction(function () use ($device, $serviceRequest, $photos) {
                    $device->serviceRequests()->save($serviceRequest);
                    if ($photos) {
                        $serviceRequest->photos()->saveMany($photos);
                    }
                });

                $response['data'] = [
                    'id' => $serviceRequest->id,
                    'device_id' => $serviceRequest->device_id,
                    'type' => $serviceRequest->type,
                    'type_name' => ServiceRequest::$types[$serviceRequest->type],
                    'description' => $serviceRequest->description,
                    'request_date' => $serviceRequest->f_request_date,
                    'status' => $serviceRequest->status,
                    'status_name' => ServiceRequest::$statuses[$serviceRequest->status],
                    'photos' => $photos->transform(function ($photo) {
                        return [
                            'id' => $photo->id,
                            'image_url' => \URL::to('/uploads/service-requests/customer-' . \Auth::guard('api')->user()->customer->id . '/' . $photo->photo)];
                    })
                ];

                // customer push notifications
                $job = (new SendPushNotifications(
                    'We have received your request, one of our engineers will respond to your request shortly.',
                    $device->customer->user->appTokens))->delay(PUSH_NOTIFICATION_DELAY);
                $this->dispatch($job);

                // engineer push notifications
                $role = $serviceRequest->type == ServiceRequest::TECHNICAL ? 'TECHNICAL_SUPPORT_ENGINEER' : 'CLINICAL_SUPPORT_ENGINEER';
                $users = User::with('roles', 'appTokens')->where('active', 1)->get()->filter(function ($user) use ($role) {
                    return $user->role_name == $role;
                });

                $tokens = Collection::make([]);
                foreach ($users as $user) {
                    if (!$user->appTokens->isEmpty()) {
                        foreach($user->appTokens as $appToken) {
                            $tokens->push($appToken);
                        }
                    }

                }

                if (!$tokens->isEmpty()) {
                    $serviceRequest->load('device');
                    $job = (new SendPushNotifications(
                        'New technical support request from ' . $serviceRequest->device->customer->clinic_name . ' for device '  . $serviceRequest->device->deviceModel->name . ' serial ' . $serviceRequest->device->serial_number . '.',
                        $tokens,
                        'ENGINEER'))->delay(PUSH_NOTIFICATION_DELAY);
                    $this->dispatch($job);
                }
            }
		  }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function storeImage(Request $request)
    {
        $response = $this->response;
        $validator = \Validator::make($request->all(), [
            'image' => 'required|max:10000',
        ]);

        \Log::info($request);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                if ($request->hasFile('image')) {
                    $img = $request->file('image');
                    $filename = $img->getClientOriginalName();

                    $photo = new ServiceRequestPhoto([
                        'photo' => $filename,
                        'temp' => 1
                    ]);
                    $photo->save();

                    $customer = \Auth::guard('api')->user()->customer;
                    $this->saveImg($img, '/customer-' . $customer->id . '/' . $filename, 'service-requests');
                    $response['data'] = [
                        'id' => $photo->id,
                        'image_url' =>  \URL::to('/uploads/service-requests/customer-' . $customer->id . '/' . $photo->photo)
                    ];
                } else {
                    $response['meta'] = $this->makeErrorMeta(400, 'The image must be a file type.');
                }
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function storeImageBase64(Request $request)
    {
        $response = $this->response;
        $validator = \Validator::make($request->all(), [
            'image' => 'required',
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $data = $request->all();
                if(preg_match('/data:image\/(jpeg|png);base64,(.*)/i', $data['image'], $matches)) {
                    $imageType = $matches[1];
                    $imageData = base64_decode($matches[2]);
                    $filename = uniqid() . '.' . $imageType;

                    $photo = new ServiceRequestPhoto([
                        'photo' => $filename,
                        'temp' => 1
                    ]);
                    $photo->save();

                    $customer = \Auth::guard('api')->user()->customer;
                    $path = storage_path() . '/app/public/uploads/service-requests/customer-' . $customer->id . '/' . $filename;
                    if (!\Storage::disk('service-requests')->exists('customer-' . $customer->id)) {
                        \Storage::disk('service-requests')->makeDirectory('customer-' . $customer->id);
                    }
                    file_put_contents($path, $imageData);

                    $response['data'] = [
                        'id' => $photo->id,
                        'image_url' =>  \URL::to('/uploads/service-requests/customer-' . $customer->id . '/' . $photo->photo)
                    ];
                } else {
                    $response['meta'] = $this->makeErrorMeta(400, 'The image must be jpeg or png format.');
                }
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function show($id)
    {
        $response = $this->response;

        try {
            $serviceRequest = ServiceRequest::find($id);
            if ($serviceRequest) {
                $log = $serviceRequest->device->serviceLogs->last();
                $response['data'] = [
                    'id' => $serviceRequest->id,
                    'device' => [
                        'id' => $serviceRequest->device_id,
                        'serial_number' => $serviceRequest->device->serial_number,
                        'model' => $serviceRequest->device->deviceModel->name,
                        'service_log' => $log ? [
                            'id' => $log->id,
                            'hasPdf' => file_exists( SERVICE_REPORT_PATH.$log->id.'.pdf' ) ? 'Y' : 'N',
                            'description' => $log->description,
                            'part_number' => $log->part_number,
                            'quantity' => $log->quantity,
                            'service_date' => $log->f_service_date,
                            'counters' => Device::getCounters($serviceRequest->device, $log),
                            'engineer' => [
                                'id' => $log->user->id,
                                'full_name' => $log->user->full_name
                            ]
                        ] : '',
                    ],
                    'type' => $serviceRequest->type,
                    'type_name' => ServiceRequest::$types[$serviceRequest->type],
                    'description' => $serviceRequest->description,
                    'request_date' => $serviceRequest->f_request_date,
                    'status' => $serviceRequest->status,
                    'status_name' => ServiceRequest::$statuses[$serviceRequest->status],
                    'attended' => $serviceRequest->attended_by ? [
                        'by' => [
                            'first_name' => $serviceRequest->user->first_name,
                            'phone_number' => phone_format($serviceRequest->user->phone_number)
                        ],
                        'date' => $serviceRequest->f_attended_at,
                        'f_date' => $serviceRequest->h_attended_at,
                    ] : false,
                ];
            } else {
                $response['meta'] = $this->makeErrorMeta(404, 'Service request not found.');
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    private function processStatus($id, $status)
    {
        $response = $this->response;

        try {
            $serviceRequest = ServiceRequest::find($id);
            if ($serviceRequest) {
                $serviceRequest->status = $status;
                $serviceRequest->save();

                $response['data'] = [
                    'id' => $serviceRequest->id,
                    'status' => $serviceRequest->status,
                    'status_name' => ServiceRequest::$statuses[$serviceRequest->status],
                ];


                if ($status == ServiceRequest::ASSIGNED) {
                    $job = (new SendPushNotifications($serviceRequest->device->customer->clinic_name . ' still have an issue, please check with them again.',
                        $serviceRequest->user->appTokens,
                        'ENGINEER'))->delay(PUSH_NOTIFICATION_DELAY);
                    $this->dispatch($job);
                }
            } else {
                $response['meta'] = $this->makeErrorMeta(404, 'Service request not found.');
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function support($id)
    {
        return $this->processStatus($id, ServiceRequest::ASSIGNED);
    }

    public function close($id)
    {
        return $this->processStatus($id, ServiceRequest::CLOSED);
    }

    public function rating($id, Request $request)
    {
        $response = $this->response;
        $validator = \Validator::make($request->all(), [
            'rating' => 'required',
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $serviceRequest = ServiceRequest::find($id);
                if ($serviceRequest) {
                    $data = $request->all();

                    $requestRating = new Rating();
                    $requestRating->rating = $data['rating'];
                    $requestRating->model_id = $serviceRequest->id;
                    $requestRating->model_type = ServiceRequest::class;

                    $engineerRating = null;
                    if ($serviceRequest->attended_by) {
                        $engineerRating = new Rating();
                        $engineerRating->rating = $data['rating'];
                        $engineerRating->model_id = $serviceRequest->attended_by;
                        $engineerRating->model_type = User::class;
                    }

                    \DB::transaction(function () use ($requestRating, $engineerRating) {
                        $requestRating->save();
                        if ($engineerRating) {
                            $engineerRating->save();
                        }
                    });

                    $response['data'] = [
                        'id' => $requestRating->id,
                        'rating' => $requestRating->rating,
                    ];

                    $job = (new SendPushNotifications($serviceRequest->device->customer->clinic_name . ' has closed the request and rate you ' . $requestRating->rating . ' stars.',
                        $serviceRequest->user ? $serviceRequest->user->appTokens : Collection::make([]),
                        'ENGINEER'))->delay(PUSH_NOTIFICATION_DELAY);
                    $this->dispatch($job);
                } else {
                    $response['meta'] = $this->makeErrorMeta(404, 'Service request not found.');
                }
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
