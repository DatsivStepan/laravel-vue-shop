<?php

namespace App\Listeners\WaitList;

use App\Models\ProductWaitlist;

class DeleteProductFromWaitList
{
    public function handle($event)
    {
        ProductWaitlist::query()->where('user_id', $event->order->userID)
            ->whereIn('product_id', $event->order->products()->pluck('products.id'))
            ->delete();
    }
}
