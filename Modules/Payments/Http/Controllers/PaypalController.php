<?php

namespace Modules\Payments\Http\Controllers;

use App\Models\PaymentData;
use App\Repositories\OrdersRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payments\Services\Enum\Paypal\EventTypes;
use Modules\Payments\Services\PaymentMethods\Paypal;

class PaypalController extends Controller
{
    /**
     * @var OrdersRepository
     */
    private $ordersRepository;
    /**
     * @var OrderService
     */
    private $orderService;

    public function __construct(OrdersRepository $ordersRepository, OrderService $orderService)
    {
        $this->ordersRepository = $ordersRepository;
        $this->orderService = $orderService;
    }

    public function captureOrder($tenantID, Request $request)
    {
        setTenant($tenantID);

        $data = $request->validate([
            'token' => 'required|string',
            'return_url' => 'required',
        ]);

        $this->capture($data['token']);

        return redirect()->away($data['return_url']);
    }

    public function activateSubscription($tenantID, Request $request)
    {
        setTenant($tenantID);

        $data = $request->validate([
            'subscription_id' => 'required|string',
            'return_url' => 'required',
        ]);

        $this->activate($data['subscription_id']);

        return redirect()->away($data['return_url']);
    }

    public function webhook(int $tenantID, Request $request)
    {
        setTenant($tenantID);

        $data = $request->validate([
            'event_type' => 'required',
            'resource' => 'required',
        ]);

        switch ($data['event_type']) {
            case EventTypes::CHECKOUT_ORDER_APPROVED:
                $this->capture($data['resource']['id']);
                break;
            case EventTypes::BILLING_SUBSCRIPTION_CREATED:
                $this->activate($data['resource']['id']);
        }
    }

    private function capture(string $externalID)
    {
        $order = $this->ordersRepository->findByExternalID($externalID);

        $paymentMethod = $order->paymentMethod;

        $paypalClient = new Paypal($paymentMethod);

        $captureData = json_decode(json_encode($paypalClient->captureOrder($externalID)), true);
        $paymentData = PaymentData::query()->firstOrcreate([
            'order_id' => $order->id,
        ]);
        $data = $paymentData->data ?: [];
        $data['capture'] = $captureData;

        $paymentData->update([
            'data' => $data,
        ]);

        $this->orderService->complete($order, ['payment_data_id' => $paymentData->id]);
    }

    private function activate($subscriptionID)
    {
        $order = $this->ordersRepository->findByExternalID($subscriptionID);
        $paymentMethod = $order->paymentMethod;

        $paypalClient = new Paypal($paymentMethod);

        $subscription = $paypalClient->getSubscription($subscriptionID);

        if ($subscription->status == 'ACTIVE') {
            $this->orderService->complete($order);
        }
    }
}
