<?php

namespace App\Classes\Enum\Product;

use App\Classes\Enum\AbstractEnum;

/**
 * Class EnrollStartType
 * @package App\Classes\Enum\Product
 */
class EnrollStartType extends AbstractEnum
{
    const NOW = 0;
    const TODAY = 1;
    const COURSE_START = 2;
    const SPECIFIC_DATE = 3;
}
