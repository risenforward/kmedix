<?php

namespace App\Http\Controllers\Api\Engineer;

use App\Customer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    use \ApiResponseTrait;

    public function show($id)
    {
        $response = $this->response;

        try {
            $customer = Customer::with('contactPersons')->find($id);
            if ($customer) {
                $response['data'] = [
                    'id' => $customer->id,
                    'clinic_name' => $customer->clinic_name,
                    'specialization' => $customer->specialization,
                    'phone_number' => format_phone($customer->user->phone_number),
                    'address' => $customer->address,
                    'location' => $customer->location ? json_decode($customer->location) : false,
                    'image_url' => $customer->logo_url,
                    'active' => $customer->user->active,
                    'contact_persons' => $customer->contactPersons->isEmpty() ? [] : $customer->contactPersons->transform(function ($person) {
                        return [
                            'id' => $person->id,
                            'name' => $person->full_name,
                            'phone_number' => format_phone($person->phone_number)
                        ];
                    })
                ];
            } else {
                $response['meta'] = $this->makeErrorMeta(404, 'Customer not found.');
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function location($id, Request $request)
    {
        $response = $this->response;

        $validator = \Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $customer = Customer::find($id);
                if ($customer) {
                    $data = $request->all();
                    $customer->location = json_encode([
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                    ]);
                    $customer->save();

                    $response['data'] = [
                        'id' => $customer->id,
                        'clinic_name' => $customer->clinic_name,
                        'location' => json_decode($customer->location)
                    ];
                } else {
                    $response['meta'] = $this->makeErrorMeta(404, 'Customer not found.');
                }
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
