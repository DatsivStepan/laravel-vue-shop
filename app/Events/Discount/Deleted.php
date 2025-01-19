<?php

namespace App\Events\Discount;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Deleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $discount;

    public function __construct($discount)
    {
        $this->discount = $discount;
    }
}
