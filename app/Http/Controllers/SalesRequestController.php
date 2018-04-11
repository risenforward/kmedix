<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Events\SalesRequestStatusChanged;
use App\SalesRequest;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Event;

class SalesRequestController extends Controller
{
    use \NotificationTrait;

    private static $createdMsg = 'Service request successfully created';
    private static $updatedMsg = 'Sales request successfully updated';
    private static $deletedMsg = 'Sales request successfully deleted';

    public function index()
    {
        $requests = SalesRequest::with('customer');
        $new = clone $requests;
        return view('sales-request.index', [
            'requests' =>$requests->where('status', '<>', SalesRequest::NOT_PROCESSED)->get(),
            'new' => $new
                ->where('status', SalesRequest::NOT_PROCESSED)
                ->orderBy('request_date', 'desc')
                ->get()
        ]);
    }

    public function update($id, $status, Request $request)
    {
        $salesRequest = SalesRequest::find($id);
        if ($request->method() == 'PUT') {
            $data = $request->all();
            if ($status == SalesRequest::PENDING) {
                $this->validate($request, ['notes' => 'required']);
                $salesRequest->notes = $data['notes'];
            }

            $salesRequest->status = $status;

            \DB::transaction(function () use ($salesRequest) {
                $salesRequest->save();
                //Event::fire(new SalesRequestStatusChanged($salesRequest));
            });

            /*if ($status != SalesRequest::PENDING) {
                $salesRequest->delete();
            }*/

            return redirect('/salesRequests')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('sales-request.update', [
                'request' => $salesRequest,
                'status' => $status,
            ]);
        }
    }

    public function delete($id, $status)
    {
        $salesRequest = SalesRequest::find($id);
        $salesRequest->status = $status;

        \DB::transaction(function () use ($salesRequest) {
            $salesRequest->save();
            $salesRequest->delete();
            //Event::fire(new SalesRequestStatusChanged($salesRequest));
        });

        return redirect()->back()->with([
            'alert' => ['code' => 200, 'text' => self::$deletedMsg]
        ]);
    }

    public function show($id)
    {
        return view('sales-request.details', [
            'request' => SalesRequest::with('customer')->find($id),
        ]);
    }
}
