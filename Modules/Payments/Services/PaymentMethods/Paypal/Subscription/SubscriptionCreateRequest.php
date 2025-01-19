<?php

namespace Modules\Payments\Services\PaymentMethods\Paypal\Subscription;

use PayPalHttp\HttpRequest;

class SubscriptionCreateRequest extends HttpRequest
{
    public function __construct()
    {
        parent::__construct("/v1/billing/subscriptions?", "POST");
        $this->headers["Content-Type"] = "application/json";
    }

    public function buildRequestBody(array $params)
    {
        $this->body = [
            'plan_id' => $params['plan_id'],
            //duplicate
            'application_context' => [
                'return_url' => route('api.payments.paypal.activateSubscription',
                    [
                        'return_url' => $params['return_url'],
                        'tenant_id' => tenant('id'),
                    ]),
                'cancel_url' => $params['cancel_url'],
                'brand_name' => 'EXAMPLE INC',
                'locale' => 'en-US', //TODO need set locale connection
                'landing_page' => 'BILLING',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'SUBSCRIBE_NOW',
            ],
        ];
    }
}
