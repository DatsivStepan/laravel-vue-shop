<?php

namespace App\Events\DebugLog;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DebugLogDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $debug_id;

    /**
     * Create a new event instance.
     *
     * @param int $debugId
     * @return void
     */
    public function __construct(int $debugId)
    {
        $this->debug_id = $debugId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
