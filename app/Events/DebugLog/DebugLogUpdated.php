<?php

namespace App\Events\DebugLog;

use App\Models\DebugLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DebugLogUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $debug_log;

    /**
     * Create a new event instance.
     *
     * @param DebugLog $product
     * @return void
     */
    public function __construct(DebugLog $product)
    {
        $this->debug_log = $product;
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
