<?php

namespace App\Console\Commands;

use App\Device;
use App\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeviceWarranty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warranty:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Devices warranty check';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $devices = Device::with([
            'customer' => function ($customer) {
                $customer->with('user');
            },
            'notification'
        ])->get()->filter(function ($device) {
            return Carbon::parse($device->install_date)->addMonth($device->warranty) < Carbon::now();
        });

        if (!$devices->isEmpty()) {
            foreach ($devices as $device) {
                if (!$device->notification) {
                    $notify = new Notification([
                        'user_id' => $device->customer->user->id,
                        'status' => Notification::IS_NEW,
                    ]);
                    $device->notification()->save($notify);
                }
            }
        }
    }
}
