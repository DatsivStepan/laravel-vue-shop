<?php

namespace App\Services\Discount\TypeRule;

use App\Facades\DiscountValidator;
use App\Models\Discount;
use App\Models\Orders;
use Illuminate\Database\Eloquent\Builder;

class AllProducts implements DiscountTypeRule
{
    public function passes(Discount $discount, Orders $order): bool
    {
        $countProductWithDiscountInOrder = $discount->products()->whereHas('orders',
            function (Builder $query) use ($order) {
                $query->whereKey($order->id);
            })->count();

        $countProductsWithDiscount = $discount->products()->count();

        $allProductInOrder = $countProductWithDiscountInOrder === $countProductsWithDiscount;

        return $allProductInOrder && DiscountValidator::validate($discount, $order->user);
    }
}
