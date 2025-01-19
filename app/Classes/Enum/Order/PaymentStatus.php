<?php

namespace App\Classes\Enum\Order;

use App\Classes\Enum\AbstractEnum;

class PaymentStatus extends AbstractEnum
{
    const PAYMENT_STATUS_IN_CART = 0;
    const PAYMENT_STATUS_PENDING = 1;
    const PAYMENT_STATUS_COMPLETED = 2;
    const PAYMENT_STATUS_REFUNDED_PARTIAL = 3;
    const PAYMENT_STATUS_REFUND = 4;
    const PAYMENT_STATUS_REJECTED = 5;
}
