<?php

namespace App\Classes\Enum\Order;

use App\Classes\Enum\AbstractEnum;

class BillingType extends AbstractEnum
{
    const SUBSCRIPTION = 'subscription';
    const REGULAR = 'regular';
    const INSTALLMENT = 'installment';
}
