<?php

namespace App\Classes\Enum\Subscriptions;

use App\Classes\Enum\AbstractEnum;

class RecurringPeriod extends AbstractEnum
{
    public const DAY = 'DAY';
    public const WEEK = 'WEEK';
    public const MONTH = 'MONTH';
    public const YEAR = 'YEAR';
}
