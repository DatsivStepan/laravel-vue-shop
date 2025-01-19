<?php

namespace Modules\Payments\Services\PaymentMethods\Paypal\Enum;

use App\Classes\Enum\AbstractEnum;

class PaypalSubscriptionStatus extends AbstractEnum
{
    public const APPROVAL_PENDING = 'APPROVAL_PENDING';
    public const APPROVED = 'APPROVED';
    public const ACTIVE = 'ACTIVE';
    public const SUSPENDED = 'SUSPENDED';
    public const CANCELED = 'CANCELLED';
    public const EXPIRED = 'EXPIRED';
}
