<?php

namespace App\Classes\Enum\Subscriptions;

use App\Classes\Enum\AbstractEnum;

class SubscriptionStatus extends AbstractEnum
{
    public const ACTIVE = 'active';
    public const SUSPENDED = 'suspended';
    public const CANCELED = 'canceled';
    public const EXPIRED = 'expired';
}
