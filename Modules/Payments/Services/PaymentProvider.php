<?php

namespace Modules\Payments\Services;

use App\Models\Orders;
use App\Models\Refund;
use App\Models\Subscription;
use Modules\Payments\Entities\PaymentMethod;

interface PaymentProvider
{
    public function __construct(PaymentMethod $paymentMethod);

    public function makeOrder(Orders $order, array $extraData);

    public function subscribe(array $payload);

    public function refund(Orders $order, Refund $refund);

    public function checkSubscription(Subscription $subscription);
}
