<?php

namespace Modules\Payments\Services\PaymentMethods\Paypal\Subscription;

use PayPalHttp\HttpRequest;

class SubscriptionGetRequest extends HttpRequest
{
    function __construct($id)
    {
        parent::__construct("/v1/billing/subscriptions/{id}?", "GET");

        $this->path = str_replace("{id}", urlencode($id), $this->path);
        $this->headers["Content-Type"] = "application/json";
    }
}
