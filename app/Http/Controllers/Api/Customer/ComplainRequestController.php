<?php

namespace App\Http\Controllers\Api\Customer;

use App\Complain;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ComplainRequestController extends Controller
{
    use \ApiResponseTrait;

    public function store(Request $request)
    {
        $response = $this->response;

        $validator = \Validator::make($request->all(), [
            'description' => 'required',
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $data = $request->all();

                $customer = \Auth::guard('api')->user()->customer;

                $complainRequest = new Complain([
                    'description' => $data['description'],
                    'status' => Complain::NOT_PROCESSED,
                ]);

                $customer->complainRequests()->save($complainRequest);

                $response['data'] = [
                    'id' => $complainRequest->id,
                    'customer_id' => $complainRequest->customer_id,
                    'description' => $complainRequest->description,
                    'request_date' => $complainRequest->getFCreatedAt(),
                    'status' => $complainRequest->status,
                    'status_name' => Complain::$statuses[$complainRequest->status],
                ];
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
