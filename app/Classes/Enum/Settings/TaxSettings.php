<?php

namespace App\Classes\Enum\Settings;

use App\Classes\Enum\AbstractEnum;

class TaxSettings extends AbstractEnum
{
    public const ENABLE_TAXES = 'enable_taxes';
    public const ENABLE_ADVANCE_TAXES = 'enable_advance_taxes';
    public const TAX_VALUE = 'tax_value';
    public const TAX_LABEL = 'taxes_label';
    public const ENABLE_GLOBAL_TAX_VALUE = 'enable_global_tax_value';
}
