<?php

namespace Modules\Payments\Http\Controllers;

use App\Models\Orders;
use App\Models\PaymentData;
use App\Repositories\OrdersRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;

class StripeController
{
    /**
     * @var OrdersRepository
     */
    private $ordersRepository;
    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * StripeController constructor.
     * @param OrdersRepository $ordersRepository
     * @param OrderService $orderService
     */
    public function __construct(OrdersRepository $ordersRepository, OrderService $orderService)
    {
        $this->ordersRepository = $ordersRepository;
        $this->orderService = $orderService;
    }

    public function webhook(int $tenantID, Request $request)
    {
        setTenant($tenantID);

        $event = \Stripe\Event::constructFrom(
            $request->all()
        );

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                /** @var Orders $order */
                $order = $this->ordersRepository->findByExternalID($event->data->values()[0]->id);

                $paymentData = PaymentData::query()->firstOrcreate([
                    'order_id' => $order->id,
                ]);
                $data = $paymentData->data ?: [];
                $data['session_completed'] = $event->data->values()[0];

                $paymentData->update([
                    'data' => $data,
                ]);

                $this->orderService->complete($order, ['payment_data_id' => $paymentData->id]);
                break;
        }
    }
}
