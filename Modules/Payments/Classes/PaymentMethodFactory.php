<?php

namespace Modules\Payments\Classes;

use App\Classes\Enum\Order\PaymentType;
use Modules\Payments\Methods\AuthorizeMethod;
use Modules\Payments\Methods\MethodInterface;
use Modules\Payments\Methods\PayPalMethod;
use Modules\Payments\Methods\StripeMethod;

class PaymentMethodFactory
{
    /**
     * @param string $type
     * @return MethodInterface
     * @throws \Exception
     */
    public static function create(string $type): MethodInterface
    {
        switch ($type) {
            case PaymentType::PAYPAL:
                return new PayPalMethod();
            case PaymentType::STRIPE:
                return new StripeMethod();
            case PaymentType::AUTHORIZE:
                return new AuthorizeMethod();
            default:
                throw new \Exception(__('errors.payments.undefined_payment_type'));
        }
    }
}
