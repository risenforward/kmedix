<?php

namespace App\Http\Controllers\Api\Customer;

use App\Device;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DeviceController extends Controller
{
    use \ApiResponseTrait;

    public function index()
    {
        $response = $this->response;

        try {
            $user = \Auth::guard('api')->user();
            $devices = Device::with('deviceModel')
                ->where('customer_id', $user->customer->id)
                ->get();

            $devices->transform(function ($device) {
                return [
                    'id' => $device->id,
                    'serial_number' => $device->serial_number,
                    'model' => $device->deviceModel->name,
                    'image_url' => $device->deviceModel->photo_url,
                ];
            });
            $response['data'] = $devices;
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function modelsIndex()
    {
        $response = $this->response;

        try {
            $user = \Auth::guard('api')->user();
            $devices = Device::with('deviceModel')
                ->where('customer_id', $user->customer->id)
                ->get();

            $devices->transform(function ($device) {
                return $device->deviceModel;
            });

            $models = $devices->unique();
            $models->transform(function ($model) {
                return [
                    'id' => $model->id,
                    'name' => $model->name,
                ];
            });
            $response['data'] = $models;
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
