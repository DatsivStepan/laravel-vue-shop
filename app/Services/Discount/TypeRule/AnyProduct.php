<?php

namespace App\Services\Discount\TypeRule;

use App\Facades\DiscountValidator;
use App\Models\Discount;
use App\Models\Orders;
use Illuminate\Database\Eloquent\Builder;

class AnyProduct implements DiscountTypeRule
{
    public function passes(Discount $discount, Orders $order): bool
    {
        $anyProductExistInOrder = $discount->products()->whereHas('orders',
            function (Builder $query) use ($order) {
                $query->whereKey($order->id);
            })->exists();

        return $anyProductExistInOrder && DiscountValidator::validate($discount, $order->user);
    }
}
