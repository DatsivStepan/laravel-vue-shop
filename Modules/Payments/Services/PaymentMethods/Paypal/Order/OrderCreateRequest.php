<?php

namespace Modules\Payments\Services\PaymentMethods\Paypal\Order;

use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class OrderCreateRequest extends OrdersCreateRequest
{
    public function buildRequestBody(array $order, array $urls)
    {
        $purchaseUnits = [];

        foreach ($order['products'] as $product) {
            $purchaseUnits[] = [
                'reference_id' => $product['id'],
                'amount' => [
                    'currency_code' => $order['currency'],
                    'value' => $product['total'],
                    'breakdown' => [
                        'item_total' => [
                            'currency_code' => $order['currency'],
                            'value' => $product['subtotal'],
                        ],
                        'tax_total' => [
                            'currency_code' => $order['currency'],
                            'value' => $product['tax'],
                        ],
                        'discount' => [
                            'currency_code' => $order['currency'],
                            'value' => $product['discount'],
                        ],
                    ],
                ],
            ];
        }

        $this->body = [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('api.payments.paypal.capture',
                    [
                        'return_url' => $urls['return_url'],
                        'tenant_id' => tenant('id'),
                    ]),
                'cancel_url' => $urls['cancel_url'],
                'brand_name' => 'EXAMPLE INC',
                'locale' => 'en-US', //TODO need set locale connection
                'landing_page' => 'BILLING',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
            ],
            'purchase_units' => $purchaseUnits,
        ];
    }
}
