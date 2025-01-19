<?php

namespace App\Classes\Enum\Order;

use App\Classes\Enum\AbstractEnum;

class EnrolledType extends AbstractEnum
{
    const NOT_ENROLLED = 0;
    const ENROLLED = 1;
    const DELETED = 2;
}
