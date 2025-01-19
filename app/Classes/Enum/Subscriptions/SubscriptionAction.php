<?php

namespace App\Classes\Enum\Subscriptions;

use App\Classes\Enum\AbstractEnum;

class SubscriptionAction extends AbstractEnum
{
    public const UNENROLL_FROM_COURSE = 0;
    public const SUSPEND_ENROLLMENTS = 1;
    public const KEEP_ENROLLMENTS_ACTIVE = 2;

    /**
     * @return array
     */
    public static function getOptions(): array
    {
        return [
            self::UNENROLL_FROM_COURSE => __('subscriptions.actions.unenroll_from_course'),
            self::SUSPEND_ENROLLMENTS => __('subscriptions.actions.suspend_enrollments'),
            self::KEEP_ENROLLMENTS_ACTIVE => __('subscriptions.actions.keep_enrollments_active'),
        ];
    }
}
