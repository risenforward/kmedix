<?php

namespace App\Events;

use App\Events\Event;
use App\SalesRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SalesRequestStatusChanged extends Event
{
    use SerializesModels;

    public $salesRequest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SalesRequest $salesRequest)
    {
        $this->salesRequest = $salesRequest;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
