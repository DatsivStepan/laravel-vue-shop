<?php

namespace App\Services\Discount\TypeRule;

use App\Models\Discount;
use App\Models\Orders;

interface DiscountTypeRule
{
    public function passes(Discount $discount, Orders $order): bool;
}
