<?php

namespace App\Services;

use App\Classes\Enum\Order\BillingType;
use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Order\PaymentType;
use App\Classes\Enum\Subscriptions\SubscriptionStatus;
use App\Events\Subscriptions\Canceled;
use App\Events\Subscriptions\ChangeStatus;
use App\Events\Subscriptions\Expired;
use App\Events\Subscriptions\Subscribe;
use App\Events\Subscriptions\Updated;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Subscription;
use App\Repositories\Admin\SubscriptionRepository;
use App\Repositories\OrdersRepository;
use Modules\Payments\Repositories\PaymentMethodRepository;
use Modules\Payments\Services\PaymentMethods\Authorize;
use Modules\Payments\Services\PaymentMethods\Paypal;
use Modules\Payments\Services\PaymentMethods\Stripe;

class SubscriptionService
{
    /**
     * @var OrderService
     */
    private $orderService;
    /**
     * @var SubscriptionRepository
     */
    private $repository;
    /**
     * @var OrdersRepository
     */
    private $ordersRepository;
    /**
     * @var PaymentMethodRepository
     */
    private $paymentMethodRepository;

    public function __construct(
        OrderService $orderService,
        SubscriptionRepository $repository,
        OrdersRepository $ordersRepository,
        PaymentMethodRepository $paymentMethodRepository
    ) {
        $this->orderService = $orderService;
        $this->repository = $repository;
        $this->ordersRepository = $ordersRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * @param array $payload
     * @param int $userId
     * @return array
     * @throws \Throwable
     */
    public function subscribe(array $payload, int $userId): array
    {
        $product = Products::find($payload['product_id']);

        $subscription = $this->repository->create([
            'user_id' => $userId,
            'product_id' => $payload['product_id'],
            'product_name' => $product->name,
            'status' => SubscriptionStatus::ACTIVE,
            'price' => $payload['total'],
            'currency' => $payload['currency'],
            'recurring_period' => $product->recurring_period,
            'cycles' => $product->billing_cycles,
        ]);

        /** @var Orders $order */
        $order = $this->ordersRepository->create([
            'user_id' => $userId,
            'payment_status' => PaymentStatus::PAYMENT_STATUS_PENDING,
            'subtotal' => $payload['subtotal'],
            'amount' => $payload['total'],
            'discount' => $payload['discount'],
            'subscription_id' => $subscription->id,
            'billing_type' => BillingType::SUBSCRIPTION,
            'currency' => $payload['currency'],
            'base_currency' => getSetting('currency'),
            'rate' => currency(1, $payload['currency'], getSetting('currency'), false),
        ]);

        $order->products()->sync([
            $product->id => [
                'quantity' => 1,
            ],
        ]);

        $payment = [
            'payment_type' => $payload['payment_type'] ?? PaymentType::INVOICE,
            'payment_method_id' => $payload['payment_method_id'] ?? null,
        ];

        $paymentMethod = $this->paymentMethodRepository->find($payment['payment_method_id']);

        $result = [];
        switch ($payment['payment_type']) {
            case PaymentType::INVOICE:
                $result = $this->orderService->pendingOrder($order->id, $payment);
                break;
            case PaymentType::PAYPAL:
                if ($paymentMethod) {
                    $paypal = new PayPal($paymentMethod);

                    $payload['name'] = $product->name;
                    $payload['recurring_period'] = $product->recurring_period;
                    $payload['cycles'] = $product->billing_cycles;

                    $paypalOrder = $paypal->subscribe($payload);

                    $options = [
                        'payment_type' => PaymentType::PAYPAL,
                        'paypal' => $paypalOrder,
                    ];

                    $this->ordersRepository->updateIn([$order->id], [
                        'payment_method_id' => $paymentMethod->id,
                        'external_id' => $paypalOrder['id'],
                        'payment_type' => $payment['payment_type'],
                    ]);

                    $result = [
                        'status' => true,
                        'message' => __('orders.payed'),
                        'options' => $options,
                    ];
                }

                break;
            case PaymentType::STRIPE:
                if ($paymentMethod) {
                    $paypal = new Stripe($paymentMethod);

                    $payload['name'] = $product->name;
                    $payload['recurring_period'] = $product->recurring_period;
                    $payload['cycles'] = $product->billing_cycles;
                    $payload['order_id'] = $order->id;

                    $stripeSession = $paypal->subscribe($payload);

                    $options = [
                        'payment_type' => PaymentType::STRIPE,
                        'stripe' => $stripeSession,
                    ];

                    $this->ordersRepository->updateIn([$order->id], [
                        'payment_method_id' => $paymentMethod->id,
                        'external_id' => $stripeSession['sessionId'],
                        'payment_type' => $payment['payment_type'],
                    ]);


                    $result = [
                        'status' => true,
                        'message' => __('orders.payed'),
                        'options' => $options,
                    ];
                }

                break;
            case PaymentType::AUTHORIZE:
                if ($paymentMethod) {
                    $authorize = new Authorize($paymentMethod);

                    $payload['name'] = $product->name;
                    $payload['recurring_period'] = $product->recurring_period;
                    $payload['cycles'] = $product->billing_cycles;
                    $payload['order_id'] = $order->id;
                    $payload['order'] = $order;
                    $payload['user_name'] = $order->user->name;

                    $authorizeResult = $authorize->subscribe($payload);

                    $options = [
                        'payment_type' => PaymentType::AUTHORIZE,
                        'authorize' => $authorizeResult,
                    ];

                    if ($authorizeResult['status']) {
                        $this->ordersRepository->updateIn([$order->id], [
                            'payment_method_id' => $paymentMethod->id,
                            'external_id' => $authorizeResult['subscription_id'],
                            'payment_type' => $payment['payment_type'],
                        ]);

                        $this->orderService->complete($order);

                        $result = [
                            'status' => true,
                            'message' => __('orders.payed'),
                            'options' => $options,
                        ];
                    } else {
                        $result = [
                            'status' => false,
                            'error_code' => $authorizeResult['error_code'],
                            'message' => $authorizeResult['error_message'],
                            'options' => $options,
                        ];
                    }
                }

                break;
        }

        event(new Subscribe($subscription));

        return $result;
    }

    /**
     * @param $subscriptionId
     * @return Subscription
     */
    public function makeCanceled($subscriptionId): Subscription
    {
        $subscription = $this->repository->update($subscriptionId, ['status' => SubscriptionStatus::CANCELED]);

        event(new Canceled($subscription));

        return $subscription;
    }


    /**
     * @param $subscriptionId
     * @return Subscription
     */
    public function makeExpired($subscriptionId): Subscription
    {
        $subscription = $this->repository->update($subscriptionId, ['status' => SubscriptionStatus::EXPIRED]);

        event(new Expired($subscription));

        return $subscription;
    }

    /**
     * @param Subscription $subscription
     * @param string $status
     * @throws \ReflectionException
     */
    public function changeStatus(Subscription $subscription, string $status)
    {
        SubscriptionStatus::assertExists($status);

        $subscription->update([
            'status' => $status,
        ]);

        event(Updated::class);
        event(ChangeStatus::class);
    }
}
