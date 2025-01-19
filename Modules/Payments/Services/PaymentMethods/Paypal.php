<?php

namespace Modules\Payments\Services\PaymentMethods;

use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Subscriptions\SubscriptionStatus;
use App\Models\Orders;
use App\Models\Refund;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Modules\Payments\Entities\PaymentMethod;
use Modules\Payments\Services\PaymentMethods\Paypal\Enum\PaypalSubscriptionStatus;
use Modules\Payments\Services\PaymentMethods\Paypal\Order\OrderCreateRequest;
use Modules\Payments\Services\PaymentMethods\Paypal\Order\RefundRequest;
use Modules\Payments\Services\PaymentMethods\Paypal\Plan\PlanCreateRequest;
use Modules\Payments\Services\PaymentMethods\Paypal\Product\ProductCreateRequest;
use Modules\Payments\Services\PaymentMethods\Paypal\Subscription\SubscriptionActivateRequest;
use Modules\Payments\Services\PaymentMethods\Paypal\Subscription\SubscriptionCreateRequest;
use Modules\Payments\Services\PaymentMethods\Paypal\Subscription\SubscriptionGetRequest;
use Modules\Payments\Services\PaymentProvider;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class Paypal implements PaymentProvider
{
    /** @var PayPalHttpClient $client */
    private $client;
    /** @var SubscriptionService */
    private $subscriptionService;

    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->client = $this->getClient($paymentMethod->settings['client_id'], $paymentMethod->settings['secret']);
        $this->subscriptionService = app(SubscriptionService::class);
    }

    /**
     * @param Orders|null $order
     * @param array $extraData
     * @return array
     */
    public function makeOrder(Orders $order, array $extraData): array
    {
        $request = new OrderCreateRequest();
        $request->buildRequestBody($order->toArray(), $extraData['urls']);
        $request->prefer('return=representation');

        $result = $this->client->execute($request)->result;

        $paypalOrder = [
            'id' => $result->id,
            'status' => $result->status,
            'intent' => $result->intent,
        ];

        foreach ($result->links as $link) {
            $paypalOrder[$link->rel] = $link->href;
        }

        return $paypalOrder;
    }

    /**
     * @param $orderID
     * @return array|string
     */
    public function captureOrder($orderID)
    {
        $request = new OrdersCaptureRequest($orderID);
        $request->prefer('return=representation');

        return $this->client->execute($request)->result;
    }

    public function activateSubscription($subscriptionID)
    {
        $request = new SubscriptionActivateRequest($subscriptionID);

        return $this->client->execute($request)->result;
    }


    public function getSubscription($subscriptionID)
    {
        $request = new SubscriptionGetRequest($subscriptionID);

        return $this->client->execute($request)->result;
    }

    /**
     * @param array $payload
     * @return array
     */
    public function subscribe(array $payload): array
    {
        $subscription = $this->createSubscription($payload);

        return $subscription;
    }


    /**
     * @param string $name
     * @param string $type
     * @param array $optional
     * @return array
     */
    private function createProduct(string $name, string $type = 'DIGITAL', array $optional = []): array
    {
        $productCreateRequest = new ProductCreateRequest();
        $requireParams = ['name' => $name, 'type' => $type];
        $productCreateRequest->buildRequestBody(array_merge($optional, $requireParams));

        $result = $this->client->execute($productCreateRequest)->result;

        return [
            'id' => $result->id,
        ];
    }

    /**
     * @param array $payload
     * @return array
     */
    private function createPlan(array $payload): array
    {
        $product = $this->createProduct($payload['name']);
        //create plan
        $request = new PlanCreateRequest();

        $payload['product_id'] = $product['id'];
        $request->buildRequestBody($payload);

        $result = $this->client->execute($request)->result;

        return [
            'id' => $result->id,
            'product_id' => $result->product_id,
            'status' => $result->status,
        ];
    }

    private function createSubscription(array $payload): array
    {
        $plan = $this->createPlan($payload);
        $subscriptionCreateRequest = new SubscriptionCreateRequest();
        $subscriptionCreateRequest->buildRequestBody([
            'plan_id' => $plan['id'],
            'return_url' => $payload['extra_data']['urls']['return_url'],
            'cancel_url' => $payload['extra_data']['urls']['cancel_url'],
        ]);

        $res = $this->client->execute($subscriptionCreateRequest)->result;

        $subscription = [
            'id' => $res->id,
            'status' => $res->status,
        ];

        foreach ($res->links as $link) {
            $subscription[$link->rel] = $link->href;
        }

        return $subscription;
    }

    //CLIENT

    /**
     * @param $clientId
     * @param $clientSecret
     * @return PayPalHttpClient
     */
    private function getClient($clientId, $clientSecret): PayPalHttpClient
    {
        $environment = $this->environment($clientId, $clientSecret);

        return new PayPalHttpClient($environment);
    }

    /**
     * @param $clientId
     * @param $clientSecret
     * @param string $mode
     * @return ProductionEnvironment|SandboxEnvironment
     */
    private function environment($clientId, $clientSecret, $mode = 'sandbox')
    {
        if ($mode == 'production') {
            return new ProductionEnvironment($clientId, $clientSecret);
        }

        return new SandboxEnvironment($clientId, $clientSecret);
    }

    /**
     * @param Orders $order
     * @param Refund $refund
     * @return bool
     */
    public function refund(Orders $order, Refund $refund): bool
    {
        if ($order->paymentData->data) {
            $request = (new RefundRequest($order->paymentData->data['capture']['purchase_units'][0]['payments']['captures'][0]['id']));

            $request->buildRequestBody([
                'currency' => $order->currency,
                'total' => $refund->amount,
            ]);

            $res = $this->client->execute($request)->result;

            if ($res) {
                $order->update([
                    'payment_status' => $order->amount > $order->refunded() ? PaymentStatus::PAYMENT_STATUS_REFUNDED_PARTIAL : PaymentStatus::PAYMENT_STATUS_REFUND,
                ]);

                $refund->update([
                    'data' => $res,
                ]);

                return true;
            }
        }

        return false;

    }

    /**
     * @param Subscription $subscription
     * @return bool
     * @throws \ReflectionException
     */
    public function checkSubscription(Subscription $subscription): bool
    {
        $subscriptionData = $this->getSubscription($subscription->orders()->first()->external_id);

        switch ($subscriptionData->status) {
            case PaypalSubscriptionStatus::ACTIVE:
                return true;
            case PaypalSubscriptionStatus::CANCELED:
                $this->subscriptionService->changeStatus($subscription, SubscriptionStatus::CANCELED);
                break;
            case PaypalSubscriptionStatus::SUSPENDED:
                $this->subscriptionService->changeStatus($subscription, SubscriptionStatus::SUSPENDED);
                break;
            case PaypalSubscriptionStatus::EXPIRED:
                $this->subscriptionService->changeStatus($subscription, SubscriptionStatus::EXPIRED);
                break;
        }
    }
}
