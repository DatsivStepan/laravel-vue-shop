<?php

namespace Modules\Payments\Services\PaymentMethods\Authorize\Enum;

use App\Classes\Enum\AbstractEnum;

class AuthorizeSubscriptionStatus extends AbstractEnum
{
    public const ACTIVE = 'active';
    public const SUSPENDED = 'suspended';
    public const CANCELED = 'canceled';
    public const EXPIRED = 'expired';
    public const TERMINATED = 'terminated';
}
