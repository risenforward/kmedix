<?php

namespace App\Http\Controllers;

use App\Device;
use App\ServiceLog;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class ServiceLogController extends Controller
{
    public function index()
    {
        return view('service-log.index', [
            'logs' => ServiceLog::with([
                'device' => function ($device) {
                    $device->with(['deviceModel', 'customer']);
                }
            ])->orderBy('service_date', 'desc')->get()
        ]);
    }

    public function deviceIndex($id)
    {
        $device = Device::with(['serviceLogs' => function ($serviceLogs) {
            $serviceLogs->with(['device' => function ($device) {
                $device->with(['deviceModel', 'customer']);
            }, 'user']);
        }])->find($id);
        return view('service-log.index', [
            'logs' => $device->serviceLogs,
        ]);
    }

    public function userIndex($id)
    {
        $user = User::find($id);
        if ($user->role_name == 'CUSTOMER') {
            $logs = ServiceLog::with(['device' => function ($device) {
                $device->with(['deviceModel', 'customer']);
            }, 'user'])->get()->filter(function ($log) use ($user) {
                return $log->device->customer_id == $user->customer->id;
            });

            return view('service-log.index', [
                'logs' => $logs,
            ]);
        } else {
            $user = User::with(['serviceLogs' => function ($serviceLogs) {
                $serviceLogs->with(['device' => function ($device) {
                    $device->with(['deviceModel', 'customer']);
                }, 'user']);
            }])->find($id);

            return view('service-log.index', [
                'logs' => $user->serviceLogs,
            ]);
        }
    }
}
