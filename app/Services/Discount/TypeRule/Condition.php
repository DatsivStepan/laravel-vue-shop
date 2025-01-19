<?php

namespace App\Services\Discount\TypeRule;

use App\Models\Discount;
use App\Models\Orders;

class Condition implements DiscountTypeRule
{
    public function passes(Discount $discount, Orders $order): bool
    {
        return false;
    }
}
