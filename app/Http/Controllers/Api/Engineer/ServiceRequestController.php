<?php

namespace App\Http\Controllers\Api\Engineer;

use App\Device;
use App\Jobs\SendPushNotifications;
use App\ServiceRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ServiceRequestController extends Controller
{
    use \ApiResponseTrait;

    protected $user = null;
    protected $type = null;

    public function __construct()
    {
        $this->user = \Auth::guard('api')->user();
        $this->type = $this->user->hasRole('TECHNICAL_SUPPORT_ENGINEER') ? ServiceRequest::TECHNICAL : ServiceRequest::CLINICAL;
    }

    public function index()
    {
        $response = $this->response;

        try {
            $assigned = ServiceRequest::with(['device' => function ($device) { $device->with('customer'); }])
                ->where(function ($query) {
                    $query->where('type', $this->type);
                    $query->where('attended_by', $this->user->id);
                    $query->where('status', '<>', ServiceRequest::CLOSED);
                })
                ->get();

            $notAssigned = ServiceRequest::with(['device' => function ($device) { $device->with('customer'); }])
                ->where(function ($query) {
                    $query->where('type', $this->type);
                    $query->whereNull('attended_by');
                    $query->where('status', ServiceRequest::REQUESTED);
                })
                ->get();

            $response['data'] = [
                'assigned' => $assigned->isEmpty() ? [] : $assigned->transform(function ($request) {
                    return [
                        'id' => $request->id,
                        'status' => $request->status,
                        'status_name' => ServiceRequest::$statuses[$request->status],
                        'customer' => [
                            'id' => $request->device->customer_id,
                            'clinic_name' => $request->device->customer->clinic_name,
                            'specialization' => $request->device->customer->specialization,
                            'image_url' => $request->device->customer->logo_url,
                        ],
                    ];
                }),
                'not_assigned' => $notAssigned->isEmpty() ? [] : $notAssigned->transform(function ($request) {
                    return [
                        'id' => $request->id,
                        'status' => $request->status,
                        'status_name' => ServiceRequest::$statuses[$request->status],
                        'customer' => [
                            'id' => $request->device->customer_id,
                            'clinic_name' => $request->device->customer->clinic_name,
                            'specialization' => $request->device->customer->specialization,
                            'image_url' => $request->device->customer->logo_url,
                        ],
                    ];
                })
            ];
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
            $request = ServiceRequest::with('device', 'photos')->find($id);
            if ($request) {
                $response['data'] = [
                    'id' => $request->id,
                    'status' => $request->status,
                    'status_name' => ServiceRequest::$statuses[$request->status],
                    'description' => $request->description,
                    'attended' => $request->attended_by ? [
                        'by' => [
                            'first_name' => $request->user->first_name,
                            'phone_number' => phone_format($request->user->phone_number)
                        ],
                        'date' => $request->f_attended_at,
                        'f_date' => $request->h_attended_at,
                    ] : false,
                    'images' => $request->photos->isEmpty() ? [] : $request->photos->transform(function ($photo) use ($request) {
                        return [
                            'id' => $photo->id,
                            'image_url' => \URL::to('/uploads/service-requests/customer-' . $request->device->customer_id . '/' . $photo->photo),
                        ];
                    }),
                    'customer' => [
                        'id' => $request->device->customer_id,
                        'clinic_name' => $request->device->customer->clinic_name,
                        'phone_number' => $request->device->customer->user->phone_number,
                        'location' => $request->device->customer->location ? json_decode($request->device->customer->location) : false,
                        'active' => $request->device->customer->user->active,
                        'image_url' => $request->device->customer->logo_url,
                    ],
                    'device' => [
                        'id' => $request->device->id,
                        'serial_number' => $request->device->serial_number,
                        'model' => $request->device->deviceModel->name,
                        'install_date' => $request->device->getFInstallDate(),
                        'warranty_end_at' => Carbon::parse($request->device->install_date)->addMonth($request->device->warranty)->format(DEFAULT_DATE_FORMAT),
                        'consumable_warranty_end_at' => $request->device->consumable_warranty ? Carbon::parse($request->device->install_date)->addMonth($request->device->consumable_warranty)->format(DEFAULT_DATE_FORMAT) : false,
                        'image_url' => $request->device->deviceModel->photo_url,
                        'contract_level' => $request->device->contract_level,
                        'extended_warranty_end_at' => $request->device->contract_level ? Carbon::parse($request->device->extended_warranty_start)->addMonth($request->device->extended_warranty)->format(DEFAULT_DATE_FORMAT) : false,
                    ]
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

    public function attend($id, Request $request)
    {
        $response = $this->response;

        $data = $request->all();
        $validator = \Validator::make($request->all(), [
            'date' => 'required',
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $serviceRequest = ServiceRequest::find($id);
                if ($serviceRequest) {
                    if (!$serviceRequest->attended_by) {
                        $serviceRequest->attended_by = $this->user->id;
                        $serviceRequest->attended_at = Carbon::parse($data['date']);
                        $serviceRequest->status = ServiceRequest::ASSIGNED;
                        $serviceRequest->save();

                        $response['data'] = [
                            'id' => $serviceRequest->id,
                            'status' => $serviceRequest->status,
                            'status_name' => ServiceRequest::$statuses[$serviceRequest->status],
                            'attended' => [
                                'id' => $this->user->id,
                                'name' => $this->user->first_name,
                                'role' => $this->user->role_name,
                            ]
                        ];

                        // customer push notifications
                        $job = (new SendPushNotifications(
                            $this->user->first_name . ' will visit you on ' .  Carbon::parse($data['date'])->formatLocalized('%A %d %B') . ' at ' . Carbon::parse($data['date'])->format('h:i A') . '.',
                            $serviceRequest->device->customer->user->appTokens))->delay(PUSH_NOTIFICATION_DELAY);
                        $this->dispatch($job);

                        // engineer push notifications
                        $users = User::with('roles', 'appTokens')->where(function ($query) {
                            $query->where('active', 1);
                            $query->where('id', '<>', $this->user->id);
                        })->get()->filter(function ($user) use ($serviceRequest) {
                            return $user->role_name == $this->user->role_name;
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
                            $job = (new SendPushNotifications(
                                $serviceRequest->device->customer->clinic_name . ' request has been assigned to ' . $this->user->first_name . '.',
                                $tokens,
                                'ENGINEER'))->delay(PUSH_NOTIFICATION_DELAY);
                            $this->dispatch($job);
                        }
                    } else {
                        $response['meta'] = $this->makeErrorMeta(409, 'Service request is already attended.');
                    }
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

    protected function processStatus($request, $status)
    {
        $request->completed_at = Carbon::now();
        $request->status = $status;
        $request->save();

        return [
            'id' => $request->id,
            'status' => $request->status,
            'status_name' => ServiceRequest::$statuses[$request->status],
        ];
    }

    public function complete($id)
    {
        $response = $this->response;

        try {
            $request = ServiceRequest::find($id);
            if ($request) {
                if ($request->status != ServiceRequest::COMPLETED) {
                    $response['data'] = $this->processStatus($request, ServiceRequest::COMPLETED);

                    $job = (new SendPushNotifications(
                        'Your support request has been completed. Please close the request and rate the service.',
                        $request->device->customer->user->appTokens))->delay(PUSH_NOTIFICATION_DELAY);
                    $this->dispatch($job);
                } else {
                    $response['meta'] = $this->makeErrorMeta(409, 'Service request is already completed.');
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

    public function pending($id)
    {
        $response = $this->response;

        try {
            $request = ServiceRequest::find($id);
            if ($request) {
                if ($request->status != ServiceRequest::PENDING) {
                    $response['data'] = $this->processStatus($request, ServiceRequest::PENDING);
                } else {
                    $response['meta'] = $this->makeErrorMeta(409, 'Service request is already pending.');
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

    public function reschedule($id)
    {
        $response = $this->response;

        try {
            $serviceRequest = ServiceRequest::find($id);
            if ($serviceRequest) {
                if ($serviceRequest->attended_by) {
                    $serviceRequest->attended_by = null;
                    $serviceRequest->attended_at = null;
                    $serviceRequest->status = ServiceRequest::REQUESTED;
                    $serviceRequest->save();

                    $response['data'] = [
                        'id' => $serviceRequest->id,
                        'status' => $serviceRequest->status,
                        'status_name' => ServiceRequest::$statuses[$serviceRequest->status],
                    ];
                } else {
                    $response['meta'] = $this->makeErrorMeta(409, 'Service request is already rescheduled.');
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
}
