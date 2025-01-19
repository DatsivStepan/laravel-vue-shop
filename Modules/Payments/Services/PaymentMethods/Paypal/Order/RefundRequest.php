<?php

namespace Modules\Payments\Services\PaymentMethods\Paypal\Order;

use PayPalHttp\HttpRequest;

class RefundRequest extends HttpRequest
{
    function __construct($id)
    {
        parent::__construct("/v1/payments/capture/$id/refund?", "POST");
        $this->headers["Content-Type"] = "application/json";
    }

    public function buildRequestBody(array $params)
    {
        $this->body = [
            'amount' => [
                'currency' => $params['currency'],
                'total' => $params['total'],
            ],
        ];
    }
}
