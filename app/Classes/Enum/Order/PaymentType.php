<?php

namespace App\Classes\Enum\Order;

use App\Classes\Enum\AbstractEnum;

class PaymentType extends AbstractEnum
{
    const INVOICE = 'invoice';
    const PAYPAL = 'paypal';
    const STRIPE = 'stripe';
    const AUTHORIZE = 'authorize';
}
