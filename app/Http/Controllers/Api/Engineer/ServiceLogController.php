<?php

namespace App\Http\Controllers\Api\Engineer;

use App\Device;
use App\ServiceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Knp\Snappy\Pdf;
use App\User;
use Mail;

class ServiceLogController extends Controller
{
    use \ApiResponseTrait;

    public function index($id)
    {
        $response = $this->response;

        try {
            $device = Device::with(['serviceLogs' => function ($serviceLogs) {
                $serviceLogs->with('user');
            }, 'deviceModel'])->find($id);

            if ($device) {
                $response['data'] = [
                    'id' => $device->id,
                    'serial_number' => $device->serial_number,
                    'model' => $device->deviceModel->name,
                    'service_logs' => $device->serviceLogs->isEmpty() ? [] : $device->serviceLogs->transform(function ($log) use ($device) {
                        return [
                            'id' => $log->id,
                            'hasPdf' => file_exists( SERVICE_REPORT_PATH.$log->id.'.pdf' ) ? 'Y' : 'N',
                            'description' => $log->description,
                            'part_desc' => $log->desc,
                            'quantity' => $log->quantity,
                            'install_date' => $device->getFInstallDate(),
                            'service_date' => $log->f_service_date,
                            'counters' => Device::getCounters($device, $log),
                            'engineer' => [
                                'id' => $log->user->id,
                                'full_name' => $log->user->full_name
                            ]
                        ];
                    }),
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

    public function create($id)
    {
        $response = $this->response;

        try {
            $device = Device::find($id);
            if ($device) {
                $response['data'] = [
                    'id' => $device->id,
                    'serial_number' => $device->serial_number,
                    'model' => $device->deviceModel->name,
                    'image_url' => $device->deviceModel->photo_url,
                    'counters' => $device->getCountersNames()
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

    public function store($id, Request $request)
    {
        $response = $this->response;

        $validator = \Validator::make($request->all(), [
            'description' => 'required',
            'service_date' => 'required',
            'part_number' => 'max:255',
            'quantity' => 'integer',
        ]);

        try {
            if ($validator->fails()) {
                $response['meta'] = $this->getValidatorErrorMeta($validator);
            } else {
                $device = Device::with([
                    'serviceLogs' => function ($serviceLogs) {
                        $serviceLogs->with('user');
                    },
                    'deviceModel'
                ])->find($id);

                if ($device) {
                    $data = $request->all();

                    $log = new ServiceLog();
                    $log->user_id = \Auth::guard('api')->user()->id;
                    $log->description = $data['description'];
                    $log->service_date = Carbon::parse($data['service_date']);
                    $log->labor_hours = isset($data['laborHours']) ? $data['laborHours'] : '0';
                    $log->complain = isset($data['complain']) && $data['complain'] != '' ? $data['complain'] : null;
                    $log->job_type = isset($data['jobType']) && $data['jobType'] != '' ? $data['jobType'] : null;
                    $log->payment = isset($data['payment']) && $data['payment'] != '' ? $data['payment'] : null;

                    /* ------  Spare Part Used  -------- */
                    $spudata = $data['spu'];
                    $qty = $partno = $desc = array();
                    foreach ($spudata as $spu) {
                        array_push($qty, $spu['qty']);
                        array_push($partno, $spu['partno']);
                        array_push($desc, $spu['desc']);
                    }

                    $log->quantity = implode(';', $qty);
                    $log->part_number = implode(';', $partno);
                    $log->desc = implode(';', $desc);

                    /* --------------------------------- 

                    if (isset($data['counters'][2])) 
                    {
                        if (isset($data['counters'][0])) {
                            $log->counter_3 = $data['counters'][0];
                        }
                        if (isset($data['counters'][1])) {
                            $log->counter_1 = $data['counters'][1];
                        }
                        $log->counter_2 = $data['counters'][2];
                    }
                    else {
                        */
                      if (isset($data['counters'][0])) {
                          $log->counter_1 = $data['counters'][0];
                      }
                      if (isset($data['counters'][1])) {
                          $log->counter_2 = $data['counters'][1];
                      }
                       if (isset($data['counters'][2])) {
                           $log->counter_3 = $data['counters'][2];
                        }


                    $device->serviceLogs()->save($log);
                    $device->load('serviceLogs');

                    $response['data'] = [
                        'id' => $device->id,
                        'serial_number' => $device->serial_number,
                        'model' => $device->deviceModel->name,
                        'service_logs' => $device->serviceLogs->isEmpty() ? [] : $device->serviceLogs->transform(function ($log) use ($device) {
                            return [
                                'id' => $log->id,
                                'description' => $log->description,
                                'part_desc' => $log->desc,
                                'quantity' => $log->quantity,
                                'install_date' => $device->getFInstallDate(),
                                'service_date' => $log->f_service_date,
                                'counters' => Device::getCounters($device, $log),
                                'engineer' => [
                                    'id' => $log->user->id,
                                    'full_name' => $log->user->full_name
                                ]
                            ];
                        }),
                    ];

                    /* ----------- generate PDF ---------- */ 
                    
                    $report = [
                        'device' => $device,
                        'log' => $log,
                        'spu' => $spudata
                    ];
                    $snappy = new Pdf('/usr/local/bin/wkhtmltopdf-amd64');
                    $snappy->generateFromHtml(view('report', [ 'report' => $report ]), SERVICE_REPORT_PATH.$log->id.'.pdf');

                    /* ----------- ------------ ---------- */
                    $customer = User::find($device->customer->user_id);
                    Mail::send('report', [ 'report' => $report ], function ($m) use ($customer) {
                        $m->to($customer->email, $customer->full_name)->subject('Your Report is sent successfully!');
                    });


                } else {
                    $response['meta'] = $this->makeErrorMeta(404, 'Device not found.');
                }
            }
        } catch (\Exception $e) {
            //$response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
