<?php

namespace App\Listeners;

use App\Events\ComplainStatusChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ComplainStatusNotification
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
     * @param  ComplainStatusChanged  $event
     * @return void
     */
    public function handle(ComplainStatusChanged $event)
    {
        //
    }
}
