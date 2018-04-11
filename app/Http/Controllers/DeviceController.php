<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Device;
use App\PreventiveMaintenance;
use App\Supplier;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    use \ImageTrait;

    private static $createdMsg = 'Device successfully created';
    private static $updatedMsg = 'Device successfully updated';
    private static $deletedMsg = 'Device successfully deleted';

    public function index($id = null)
    {
        if ($id) {
            return view('device.index', [
                'devices' => Device::where('customer_id', $id)->get()->load('deviceModel', 'customer')
            ]);
        } else {
            return view('device.index', [
                'devices' => Device::all()->load('deviceModel', 'customer')
            ]);
        }
    }

    public function create(Request $request)
    {
        if ($request->method() == 'POST') {
            $this->validate($request, Device::$rules);

            $data = $request->all();
            $device = Device::prepare($data);
            $schedule = PreventiveMaintenance::getSchedule($data['install_date'], $data['warranty']);

            $extSchedule = null;
            if (isset($data['extended_warranty_active'])) {
                $extSchedule = PreventiveMaintenance::getSchedule($data['extended_warranty_start'], $data['extended_warranty'], true);
            }

            DB::transaction(function () use($device, $schedule, $extSchedule) {
                $device->save();
                $device->preventiveMaintenances()->saveMany($schedule);
                if ($extSchedule) {
                    $device->preventiveMaintenances()->saveMany($extSchedule);
                }
            });

            return redirect('/devices')->with([
                'alert' => ['code' => 200, 'text' => self::$createdMsg]
            ]);
        } else {
            return view('device.create', [
                'suppliers' => Supplier::where('active', 1)->get(),
                'customers' => Customer::active(true),
                'users' => User::active('TECHNICAL_SUPPORT_ENGINEER', true),
            ]);
        }
    }

    public function update($id, Request $request)
    {
        $device = Device::find($id);
        if ($request->method() == 'PUT') {
            $this->validate($request, $this->prepareRules(Device::$rules, [
                'serial_number' => $device->id,
            ]));

            $data = $request->all();

            $schedule = null;
            if ($data['install_date'] != $device->install_date || $data['warranty'] != $device->warranty) {
                $schedule = PreventiveMaintenance::getSchedule($data['install_date'], $data['warranty']);
            }

            $extSchedule = null;
            if (isset($data['extended_warranty_active']) && ($data['extended_warranty_start'] != $device->extended_warranty_start || $data['extended_warranty'] != $device->extended_warranty)) {
                $extSchedule = PreventiveMaintenance::getSchedule($data['extended_warranty_start'], $data['extended_warranty'], true);
            }

            $device = Device::prepare($data, $device);

            DB::transaction(function () use($device, $schedule, $extSchedule, $data) {
                $device->save();
                if ($schedule) {
                    PreventiveMaintenance::deleteMaintenances($device);
                    $device->preventiveMaintenances()->saveMany($schedule);
                }
                if ($extSchedule) {
                    PreventiveMaintenance::deleteMaintenances($device, 1);
                    $device->preventiveMaintenances()->saveMany($extSchedule);
                } else if (!isset($data['extended_warranty_active'])) {
                    PreventiveMaintenance::deleteMaintenances($device, 1);
                }
            });

            return redirect('/devices')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('device.update', [
                'device' => $device,
                'suppliers' => Supplier::where('active', 1)->get(),
                'customers' => Customer::active(true),
                'users' => User::active('TECHNICAL_SUPPORT_ENGINEER', true),
            ]);
        }
    }

    public function details($id)
    {
        $device = Device::find($id)->load(
            'deviceModel',
            'customer',
            'user',
            'serviceRequests',
            'preventiveMaintenances'
        );
        $device->preventiveMaintenances->filter(function ($maintenance) {
            return $maintenance->maintenance_date > Carbon::now();
        });
        return view('device.details', [
            'device' => $device
        ]);
    }

    public function warranty($id, Request $request)
    {
        $device = Device::find($id);
        if ($request->method() == 'PUT') {
            $this->validate($request, Device::$extWarrantyRules);

            $data = $request->all();

            $device = Device::prepare($data, $device);
            $extSchedule = PreventiveMaintenance::getSchedule($data['extended_warranty_start'], $data['extended_warranty'], true);

            DB::transaction(function () use($device, $extSchedule) {
                $device->save();
                $device->preventiveMaintenances()->saveMany($extSchedule);
            });

            return redirect('/device/' . $device->id . '/details')->with([
                'alert' => ['code' => 200, 'text' => self::$updatedMsg]
            ]);
        } else {
            return view('device.warranty', ['device' => $device]);
        }
    }

    public function serviceLog($id)
    {
        $device = Device::with([
            'deviceModel',
            'customer',
            'serviceLogs' => function ($serviceLogs) {
                $serviceLogs->with('user');
            }
        ])->find($id);
        return view('device.service-log', ['device' => $device]);
    }

    public function serviceReport($id)
    {
        return view('device.service-report', [
            'device' => Device::with(['deviceModel', 'customer',])->find($id)
        ]);
    }
}
