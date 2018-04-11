<?php

namespace App\Http\Controllers\Api\Engineer;

use App\PreventiveMaintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PreventiveMaintenanceController extends Controller
{
    use \ApiResponseTrait;

    public function index(Request $request)
    {
        $response = $this->response;

        try {
            $data = $request->all();

            $date = Carbon::now();
            if (isset($data['date'])) {
                $date = Carbon::parse($data['date']);
            }
            $tasks = PreventiveMaintenance::with([
                'device' => function ($device) {
                    $device->with('deviceModel', 'customer');
                }
            ])->where(function ($query) use ($date, $data) {
                $query->whereBetween(
                    'maintenance_date', [
                        $date->startOfMonth()->format(DEFAULT_DB_FORMAT),
                        $date->endOfMonth()->format(DEFAULT_DB_FORMAT)
                    ]
                );
                if (isset($data['completed']) && $data['completed'] != 2) {
                    $query->where('completed', $data['completed']);
                }
            })->whereHas('device', function($query) use ($date) {
          		  $query->where('preventive_maintenance', true);
          	})->get();

            $response['data'] = $tasks->isEmpty() ? [] : $tasks->transform(function ($task) {
                return [
                    'id' => $task->id,
                    'maintenance_date' => $task->f_maintenance_date,
                    'completed' => $task->completed,
                    'completed_at' => $task->f_completed_at,
                    'device' => [
                        'id' => $task->device->id,
                        'serial_number' => $task->device->serial_number,
                        'model' => $task->device->deviceModel->name,
                    ],
                    'customer' => [
                        'id' => $task->device->customer->id,
                        'clinic_name' => $task->device->customer->clinic_name,
                    ]
                ];
            });
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function complete($id)
    {
        $response = $this->response;

        try {
            $task = PreventiveMaintenance::find($id);

            if ($task && !$task->completed) {
                $task->completed = 1;
                $task->completed_at = Carbon::now();
                $task->save();

                $response['data'] = [
                    'id' => $task->id,
                    'completed' => $task->completed,
                    'completed_at' => $task->f_completed_at,
                ];
            } else if ($task && $task->completed) {
                $response['meta'] = $this->makeErrorMeta(409, 'Task is already completed.');
            } else {
                $response['meta'] = $this->makeErrorMeta(404, 'Task not found.');
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }

    public function uncomplete($id)
    {
        $response = $this->response;

        try {
            $task = PreventiveMaintenance::find($id);

            if ($task && $task->completed) {
                $task->completed = 0;
                $task->completed_at = null;
                $task->save();

                $response['data'] = [
                    'id' => $task->id,
                    'completed' => $task->completed,
                    'completed_at' => $task->f_completed_at,
                ];
            } else if ($task && !$task->completed) {
                $response['meta'] = $this->makeErrorMeta(409, 'Task is already uncompleted.');
            } else {
                $response['meta'] = $this->makeErrorMeta(404, 'Task not found.');
            }
        } catch (\Exception $e) {
            $response['meta'] = $this->getInternalServerErrorMeta($e);
        } finally {
            return $this->response($response);
        }
    }
}
