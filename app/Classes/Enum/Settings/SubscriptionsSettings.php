<?php

namespace App\Classes\Enum\Settings;

use App\Classes\Enum\AbstractEnum;

class SubscriptionsSettings extends AbstractEnum
{
    public const ENABLE_SUBSCRIPTION = 'enable_subscription';
    public const SUSPEND_THRESHOLD = 'suspend_threshold';
    public const NOTIFICATION_THRESHOLD = 'notification_threshold';
    public const ENABLE_SUBSCRIPTIONS_COUPONS = 'enable_subscriptions_coupons';
    public const SHOW_TOTAL_SUBSCRIPTION_PRICE_OVER_WHOLE_PERIOD = 'show_total_subscription_price_over_whole_period';
    public const ENABLE_CATALOG_SUBSCRIPTIONS = 'enable_catalog_subscriptions';
    public const ALLOW_ONE_ACTIVE_CATALOG_SUBSCRIPTION = 'allow_one_active_catalog_subscription';
    public const ENABLE_SUBSCRIPTIONS_ENROLLMENTS_ACTION = 'enable_subscriptions_enrollments_action';

}
