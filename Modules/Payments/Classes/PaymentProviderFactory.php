<?php

namespace Modules\Payments\Classes;

use App\Classes\Enum\Order\PaymentType;
use Modules\Payments\Entities\PaymentMethod;
use Modules\Payments\Services\PaymentMethods\Authorize;
use Modules\Payments\Services\PaymentMethods\Paypal;
use Modules\Payments\Services\PaymentMethods\Stripe;
use Modules\Payments\Services\PaymentProvider;

class PaymentProviderFactory
{
    /**
     * @param PaymentMethod $paymentMethod
     * @return PaymentProvider
     * @throws \Exception
     */
    public static function create(PaymentMethod $paymentMethod): PaymentProvider
    {
        switch ($paymentMethod->type) {
            case PaymentType::PAYPAL:
                return new Paypal($paymentMethod);
            case PaymentType::STRIPE:
                return new Stripe($paymentMethod);
            case PaymentType::AUTHORIZE:
                return new Authorize($paymentMethod);
            default:
                throw new \Exception(__('errors.payments.undefined_payment_provider'));
        }
    }
}
