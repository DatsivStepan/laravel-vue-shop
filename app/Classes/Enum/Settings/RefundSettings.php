<?php

namespace App\Classes\Enum\Settings;

use App\Classes\Enum\AbstractEnum;

class RefundSettings extends AbstractEnum
{
    public const ENABLE_REFUND = 'enable_refund';
    public const ENABLE_MANUAL_REFUND = 'enable_manual_refund';
    public const REFUND_REASONS = 'refund_reasons';
    public const ACTION_AFTER_REFUND = 'action_after_refund';
}
