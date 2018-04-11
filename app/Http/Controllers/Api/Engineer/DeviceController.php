<?php

namespace App\Http\Controllers\Api\Engineer;

use App\Customer;
use App\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DeviceController extends Controller
{
    use \ApiResponseTrait;

    public function index(Request $request, $id = null)
    {
        $response = $this->response;

        try {
            if ($id) {
                $customer = Customer::with(['devices' => function ($devices) {
                    $devices->with('deviceModel');
                }])->find($id);

                if ($customer) {
                    $response['data'] = $customer->devices->isEmpty() ? [] : $customer->devices->transform(function ($device) use ($customer) {
                        return [
                            'id' => $device->id,
                            'serial_number' => $device->serial_number,
                            'model' => $device->deviceModel->name,
                            'clinic_name' => $customer->clinic_name,
                        ];
                    });
                } else {
                    $response['meta'] = $this->makeErrorMeta(404, 'Customer not found.');
                }
            } else {
                $devices = Device::with('customer', 'deviceModel')->get();
                $response['data'] = [
                    'devices' => $devices->isEmpty() ? [] : $devices->transform(function ($device) {
                        return [
                            'id' => $device->id,
                            'serial_number' => $device->serial_number,
                            'model' => $device->deviceModel->name,
                            'clinic_name' => $device->customer->clinic_name,
                        ];
                    })
                ];
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function search(Request $request)
    {
        $response = $this->response;

        try {
            $data = $request->all();
            $devices = Device::with('customer', 'deviceModel')->get()->filter(function ($device) use ($data) {
                $flag = false;
                if (isset($data['customer'])) {
                    $flag = strpos($device->customer->clinic_name, $data['customer']) !== false;
                }
                if (isset($data['model'])) {
                    $flag = strpos($device->deviceModel->name, $data['model']) !== false;
                }

                return $flag;
            });

            $response['data'] = $devices->isEmpty() ? [] : $devices->transform(function ($device) {
                return [
                    'id' => $device->id,
                    'serial_number' => $device->serial_number,
                    'model' => $device->deviceModel->name,
                    'clinic_name' => $device->customer->clinic_name,
                ];
            });
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
            $device = Device::with([
                'deviceModel' => function ($deviceModel) {
                    $deviceModel->with('supplier');
                },
                'customer',
            ])->find($id);

            if ($device) {
                $response['data'] = [
                    'id' => $device->id,
                    'serial_number' => $device->serial_number,
                    'model' => $device->deviceModel->name,
                    'install_date' => $device->getFInstallDate(),
                    'warranty_end_at' => Carbon::parse($device->install_date)->addMonth($device->warranty)->format(DEFAULT_DATE_FORMAT),
                    'consumable_warranty_end_at' => $device->consumable_warranty ? Carbon::parse($device->install_date)->addMonth($device->consumable_warranty)->format(DEFAULT_DATE_FORMAT) : false,
                    'clinic_id' => $device->customer->id,
                    'clinic_name' => $device->customer->clinic_name,
                    'active' => $device->customer->user->active,
                    'supplier' => $device->deviceModel->supplier->name,
                    'image_url' => $device->deviceModel->photo_url,
                     'contract_level' => $device->contract_level,
                    'extended_warranty_end_at' => $device->contract_level ? Carbon::parse($device->extended_warranty_start)->addMonth($device->extended_warranty)->format(DEFAULT_DATE_FORMAT) : false,
               ];
            } else {
                $response['meta'] = $this->makeErrorMeta(404, 'Device not found.');
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
