<?php


namespace Modules\Payments\Services\PaymentMethods\Paypal\Plan;


use PayPalHttp\HttpRequest;

class PlanCreateRequest extends HttpRequest
{
    function __construct()
    {
        parent::__construct("/v1/billing/plans?", "POST");
        $this->headers["Content-Type"] = "application/json";
    }

    public function buildRequestBody(array $params)
    {
        $this->body = [
            'product_id' => $params['product_id'],
            'name' => $params['name'],
            'status' => 'ACTIVE',
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit' => $params['recurring_period'],
                        'interval_count' => 1,
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1, // order for cycles
                    'total_cycles' => $params['cycles'],
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'currency_code' => $params['currency'],
                            'value' => $params['total'],
                        ],
                    ],
                ],
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
            ],
        ];
    }
}
