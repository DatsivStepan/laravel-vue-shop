<?php

namespace Modules\Payments\Services\PaymentMethods\Paypal\Subscription;

use PayPalHttp\HttpRequest;

class SubscriptionActivateRequest extends HttpRequest
{
    function __construct($id)
    {
        parent::__construct("/v1/billing/subscriptions/{id}/activate?", "POST");

        $this->path = str_replace("{id}", urlencode($id), $this->path);
        $this->headers["Content-Type"] = "application/json";
    }
}
