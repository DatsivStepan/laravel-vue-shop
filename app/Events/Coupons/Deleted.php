<?php


namespace App\Events\Coupons;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Deleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $coupon;

    public function __construct($coupon)
    {
        $this->coupon = $coupon;
    }
}
