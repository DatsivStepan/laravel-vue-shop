<?php

namespace App\Events\Order;

use App\Models\Orders;
use Illuminate\Queue\SerializesModels;

class CompletedOrder
{
    use SerializesModels;

    /** @var Orders */
    public $order;

    public function __construct(Orders $order)
    {
        $this->order = $order;
    }
}
