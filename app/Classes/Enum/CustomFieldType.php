<?php

namespace App\Classes\Enum;

/**
 * Class CustomFieldType
 * @package App\Classes\Enum
 */
class CustomFieldType extends AbstractEnum
{
    const FIELD_TYPE_TEXT = 0;
    const FIELD_TYPE_SELECT = 1;
    const FIELD_TYPE_MULTISELECT = 2;
    const FIELD_TYPE_CHECKBOX = 3;
    const FIELD_TYPE_TEXTAREA = 4;
    const FIELD_TYPE_DATE = 5;
    const FIELD_TYPE_DATETIME = 6;
}
