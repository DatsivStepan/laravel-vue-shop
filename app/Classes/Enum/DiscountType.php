<?php

namespace App\Classes\Enum;

/**
 * Class CouponType
 * @package App\Classes\Enum
 */
class DiscountType extends AbstractEnum
{
    const ALL = 0;
    const ANY = 1;
    const CONDITION = 3;
    const SEAT_CONDITION = 5;
    const CUMULATIVE = 7;
    const SESSIONS_CONDITION = 8;
}
