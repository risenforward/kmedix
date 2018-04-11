<?php

namespace App\Listeners;

use App\Events\SalesRequestStatusChanged;
use App\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SalesRequestStatusNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SalesRequestStatusChanged  $event
     * @return void
     */
    public function handle(SalesRequestStatusChanged $event)
    {
        $salesRequest = $event->salesRequest;

        $notification = new Notification([
            'user_id' => $salesRequest->customer->user->id,
            'data' => \GuzzleHttp\json_encode(['status' => $salesRequest->status]),
            'status' => Notification::IS_NEW,
        ]);

        $salesRequest->notifications()->save($notification);
    }
}
