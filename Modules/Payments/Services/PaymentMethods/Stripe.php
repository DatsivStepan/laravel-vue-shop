<?php

namespace Modules\Payments\Services\PaymentMethods;

use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Settings\TaxSettings;
use App\Classes\Enum\Subscriptions\SubscriptionStatus;
use App\Models\Orders;
use App\Models\Refund;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Support\Str;
use Modules\Payments\Entities\PaymentMethod;
use Modules\Payments\Services\PaymentProvider;
use Stripe\StripeClient;

class Stripe implements PaymentProvider
{
    /** @var StripeClient $client */
    private $client;
    /** @var PaymentMethod $paymentMethod */
    private $paymentMethod;
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->client = $this->getClient($paymentMethod->settings['secret_key']);
        $this->paymentMethod = $paymentMethod;
        $this->subscriptionService = app(SubscriptionService::class);
    }

    public function makeOrder(Orders $order, $extraData)
    {
        $lineItems = [];
        $payload = $order->toArray();

        foreach ($payload['products'] as $product) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => Str::lower($payload['currency']),
                    'product_data' => [
                        'name' => $product['name'],
                    ],
                    'unit_amount_decimal' => bcmul($product['discount_price'], 100),
                ],
                'quantity' => $product['quantity'],
            ];
        }

        $lineItems[] = [
            'price_data' => [
                'currency' => Str::lower($payload['currency']),
                'product_data' => [
                    'name' => getSetting(TaxSettings::TAX_LABEL) ?: __('taxes.tax'),
                ],
                'unit_amount_decimal' => bcmul($payload['tax'], 100),
            ],
            'quantity' => 1,
        ];

        $result = $this->client->checkout->sessions->create([
            'success_url' => $extraData['urls']['return_url'],
            'cancel_url' => $extraData['urls']['cancel_url'],
            'payment_method_types' => ['card'],
            'client_reference_id' => $payload['id'],
            'line_items' => $lineItems,
            'mode' => 'payment',
        ]);

        return [
            'sessionId' => $result->id,
            'publishableKey' => $this->paymentMethod->settings['publishable_key'],
        ];
    }

    /**
     * @param array $payload
     * @return array
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function subscribe(array $payload): array
    {
        $result = $this->client->checkout->sessions->create([
            'success_url' => $payload['extra_data']['urls']['return_url'],
            'cancel_url' => $payload['extra_data']['urls']['cancel_url'],
            'payment_method_types' => ['card'],
            'client_reference_id' => $payload['order_id'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => Str::lower($payload['currency']),
                        'product_data' => [
                            'name' => $payload['name'],
                        ],
                        'unit_amount_decimal' => bcmul($payload['total'], 100),
                        'recurring' => [
                            'interval' => Str::lower($payload['recurring_period']),
                            'interval_count' => $payload['cycles'],
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
        ]);

        return [
            'sessionId' => $result->id,
            'publishableKey' => $this->paymentMethod->settings['publishable_key'],
        ];
    }

    /**
     * @param string $secretKey
     * @return StripeClient
     */
    private function getClient(string $secretKey): StripeClient
    {
        return new StripeClient($secretKey);
    }

    /**
     * @param Orders $order
     * @param Refund $refund
     * @return bool
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function refund(Orders $order, Refund $refund): bool
    {
        $res = $this->client->refunds->create([
            'payment_intent' => $order->paymentData->data['session_completed']['payment_intent'],
            'amount' => bcmul($refund->amount, 100),
        ])->toArray();

        if ($res) {
            $order->update([
                'payment_status' => $order->amount > $order->refunded() ? PaymentStatus::PAYMENT_STATUS_REFUNDED_PARTIAL : PaymentStatus::PAYMENT_STATUS_REFUND,
            ]);

            $refund->update([
                'data' => $res,
            ]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $subscriptionID
     * @return \Stripe\Subscription
     * @throws \Stripe\Exception\ApiErrorException
     */
    private function getSubscription(string $subscriptionID): \Stripe\Subscription
    {
        return $this->client->subscriptions->retrieve(
            $subscriptionID,
            []
        );
    }

    /**
     * @param Subscription $subscription
     * @return bool
     * @throws \Stripe\Exception\ApiErrorException|\ReflectionException
     */
    public function checkSubscription(Subscription $subscription): bool
    {
        $subscriptionData = $this->getSubscription($subscription->orders()->first()->external_id);

        switch ($subscriptionData->status) {
            case \Stripe\Subscription::STATUS_ACTIVE:
                return true;
            case \Stripe\Subscription::STATUS_CANCELED:
                $this->subscriptionService->changeStatus($subscription, SubscriptionStatus::CANCELED);
                break;
            case \Stripe\Subscription::STATUS_UNPAID:
                $this->subscriptionService->changeStatus($subscription, SubscriptionStatus::SUSPENDED);
                break;
            case \Stripe\Subscription::STATUS_INCOMPLETE:
            case \Stripe\Subscription::STATUS_INCOMPLETE_EXPIRED:
                $this->subscriptionService->changeStatus($subscription, SubscriptionStatus::EXPIRED);
                break;
        }
    }
}
