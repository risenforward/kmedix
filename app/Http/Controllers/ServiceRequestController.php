<?php

namespace App\Http\Controllers;

use App\Device;
use App\ServiceRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

class ServiceRequestController extends Controller
{
    private static $createdMsg = 'Service request successfully created';
    private static $updatedMsg = 'Service request successfully updated';
    private static $deletedMsg = 'Service request successfully deleted';

    public function index()
    {
        $requests = ServiceRequest::with(['device' => function ($device) {
            $device->with(['deviceModel', 'customer']);
        }, 'user']);
        $unattended = clone $requests;
        return view('service-request.index', [
            'requests' => $requests->get(),
            'unattended' => $unattended
                ->where('status', \App\ServiceRequest::REQUESTED)
                ->orderBy('request_date', 'desc')
                ->get()
        ]);
    }

    public function deviceIndex($id)
    {
        $device = Device::with(['serviceRequests' => function ($serviceRequests) {
            $serviceRequests->with(['device' => function ($device) {
                $device->with(['deviceModel', 'customer']);
            }, 'user']);
        }])->find($id);
        return view('service-request.index', [
            'requests' => $device->serviceRequests,
            'unattended' => null
        ]);
    }

    public function userIndex($id)
    {
        $user = User::find($id);
        if ($user->role_name == 'CUSTOMER') {
            $requests = ServiceRequest::with(['device' => function ($device) {
                $device->with(['deviceModel', 'customer']);
            }, 'user'])->get()->filter(function ($request) use ($user) {
                return $request->device->customer_id == $user->customer->id;
            });

            return view('service-request.index', [
                'requests' => $requests,
                'unattended' => null
            ]);
        } else {
            $user = User::with(['serviceRequests' => function ($serviceRequests) {
                $serviceRequests->with(['device' => function ($device) {
                    $device->with(['deviceModel', 'customer']);
                }, 'user']);
            }])->find($id);

            return view('service-request.index', [
                'requests' => $user->serviceRequests,
                'unattended' => null
            ]);
        }
    }

    public function customerIndex($id)
    {
        $requests = ServiceRequest::with(['device' => function ($device) {
            $device->with(['deviceModel', 'customer']);
        }, 'user'])->get()->filter(function ($request) use ($id) {
            return $request->device->customer_id == $id;
        });

        return view('service-request.index', [
            'requests' => $requests,
            'unattended' => null
        ]);
    }

    public function details($id)
    {
        return view('service-request.details', [
            'request' => ServiceRequest::with(['device' => function ($device) {
                $device->with(['deviceModel', 'customer']);
            }, 'user'])->find($id)
        ]);
    }

    public function close($id)
    {
        $request = ServiceRequest::find($id);
        $request->status = ServiceRequest::CLOSED;
        $request->closed_at = Carbon::now();
        $request->save();

        return redirect()->back()->with([
            'alert' => ['code' => 200, 'text' => self::$updatedMsg]
        ]);
    }

    public function delete($id)
    {
        $request = ServiceRequest::find($id);
        $request->delete();

        return redirect('/serviceRequests')->with([
            'alert' => ['code' => 200, 'text' => self::$deletedMsg]
        ]);
    }
}
