<?php

namespace App\Http\Controllers\Api\Customer;

use App\Complain;
use App\Device;
use App\Notification;
use App\SalesRequest;
use App\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    use \ApiResponseTrait;

    protected function makeDeviceResponse($notification)
    {
        return [
            'id' => $notification->id,
            'date' => $notification->created_at->format(DEFAULT_DATETIME_FORMAT),
            'device' => [
                'id' => $notification->model->id,
                'serial_number' => $notification->model->serial_number,
                'model' => $notification->model->deviceModel->name,
                'warranty_end' => Carbon::parse($notification->model->install_date)->addMonth($notification->model->warranty)->format(DEFAULT_DATE_FORMAT),
                'image_url' => $notification->model->deviceModel->photo_url,
            ],
        ];
    }

    /*protected function makeSalesRequestResponse($notification)
    {
        $data = \GuzzleHttp\json_decode($notification->data);
        return [
            'id' => $notification->id,
            'date' => $notification->created_at->format(DEFAULT_DATETIME_FORMAT),
            'request' => [
                'id' => $notification->model->id,
                'date' => Carbon::parse($notification->model->request_date)->format(DEFAULT_DATE_FORMAT),
                'details' => $notification->model->request_details,
                'status' => $data->status,
                'status_name' => SalesRequest::$statuses[$data->status],
            ]
        ];
    }*/

    public function index()
    {
        $response = $this->response;

        try {
            $user = \Auth::guard('api')->user();

            $notifications = Notification::with(['model' => function ($model) {
                $model->withTrashed();
            }])->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                $query->where('status', Notification::IS_NEW);
            })->get();

            $response['data']['total'] = $notifications->count();

            $n1 = clone $notifications;
            $response['data']['devices_with_end_warranty'] = $n1->where('model_type', Device::class)->transform(function ($notification) {
                return $this->makeDeviceResponse($notification);
            });

            /*$n2 = clone $notifications;
            $response['data']['sales_requests'] = $n2->where('model_type', SalesRequest::class)->transform(function ($notification) {
                return $this->makeSalesRequestResponse($notification);
            });*/
            $salesRequests = SalesRequest::where(function ($query) use ($user) {
                $query->where('customer_id', $user->customer->id);
                $query->whereIn('status', [SalesRequest::NOT_PROCESSED, SalesRequest::PENDING]);
            })->get();
            $salesRequests->transform(function ($salesRequest) {
                return [
                    'id' => $salesRequest->id,
                    'date' => Carbon::parse($salesRequest->request_date)->format(DEFAULT_DATE_FORMAT),
                    'details' => $salesRequest->request_details,
                    'status' => $salesRequest->status,
                    'status_name' => SalesRequest::$statuses[$salesRequest->status],
                ];
            });

            $complainRequests = Complain::where(function ($query) use ($user) {
                $query->where('customer_id', $user->customer->id);
                $query->whereIn('status', [Complain::NOT_PROCESSED]);
            })->get();
            $complainRequests->transform(function ($complainRequest) {
                return [
                    'id' => $complainRequest->id,
                    'date' => Carbon::parse($complainRequest->created_at)->format(DEFAULT_DATE_FORMAT),
                    'description' => $complainRequest->request_details,
                    'status' => $complainRequest->status,
                    'status_name' => Complain::$statuses[$complainRequest->status],
                ];
            });

            $serviceRequests = ServiceRequest::with([
                'device' => function ($device) {
                    $device->with('deviceModel');
                },
                'photos',
                'user'
            ])->where(function ($query) use ($user) {
                $query->whereIn('device_id', Device::where('customer_id', $user->customer->id)->get()->transform(function ($device) { return $device->id; }));
                $query->where('status', '<>', ServiceRequest::CLOSED);
            })->get();
            $serviceRequests->transform(function ($serviceRequest) {
                return [
                    'id' => $serviceRequest->id,
                    'device' => [
                        'id' => $serviceRequest->device_id,
                        'serial_number' => $serviceRequest->device->serial_number,
                        'model' => $serviceRequest->device->deviceModel->name,
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
                        'date' => $serviceRequest->f_attended_at
                    ] : false,
                    'photos' => $serviceRequest->photos->transform(function ($photo) {
                        return [
                            'id' => $photo->id,
                            'image_url' => \URL::to('/uploads/service-requests/customer-' . \Auth::guard('api')->user()->customer->id . '/' . $photo->photo)];
                    })
                ];
            });

            $response['data']['total'] += count($salesRequests) + count($complainRequests) + count($serviceRequests);
            $response['data']['sales_requests'] = $salesRequests;
            $response['data']['complain_requests'] = $complainRequests;
            $response['data']['service_requests'] = $serviceRequests;
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
            $notification = Notification::with(['model' => function ($model) {
                $model->withTrashed();
            }])->find($id);

            if ($notification->status == Notification::IS_NEW) {
                $notification->status = Notification::IS_VIEW;
                $notification->save();
            }

            if ($notification->model_type == Device::class) {
                $response['data'] = $this->makeDeviceResponse($notification);
            }/* elseif ($notification->model_type == SalesRequest::class) {
                $response['data'] = $this->makeSalesRequestResponse($notification);
            }*/
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
