<?php

namespace App\Http\Controllers\Api\Customer;

use App\SalesRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SalesRequestController extends Controller
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

                $salesRequest = new SalesRequest([
                    'request_date' => Carbon::now(),
                    'request_details' => $data['description'],
                    'status' => SalesRequest::NOT_PROCESSED,
                ]);

                $customer->salesRequests()->save($salesRequest);

                $response['data'] = [
                    'id' => $salesRequest->id,
                    'customer_id' => $salesRequest->customer_id,
                    'description' => $salesRequest->request_details,
                    'request_date' => $salesRequest->f_request_date,
                    'status' => $salesRequest->status,
                    'status_name' => SalesRequest::$statuses[$salesRequest->status],
                ];
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
