<?php

namespace App\Classes\Enum\Installments;

use App\Classes\Enum\AbstractEnum;

/**
 * Class CouponType
 * @package App\Classes\Enum
 */
class FeeType extends AbstractEnum
{
    const PERCENT = 0;
    const CURRENCY = 1;
}
