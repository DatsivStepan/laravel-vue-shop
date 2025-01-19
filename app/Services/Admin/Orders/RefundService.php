<?php

namespace App\Services\Admin\Orders;

use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Order\PaymentType;
use App\Classes\Enum\Settings\RefundSettings;
use App\Exceptions\InvalidOrderRefundPaymentStatus;
use App\Models\Orders;
use App\Models\Refund;
use App\Repositories\Admin\RefundRepository;
use App\Repositories\OrdersRepository;
use Modules\Payments\Classes\PaymentProviderFactory;

class RefundService
{
    /**
     * @var OrdersRepository
     */
    private $ordersRepository;
    /**
     * @var RefundRepository
     */
    private $repository;

    public function __construct(OrdersRepository $ordersRepository, RefundRepository $repository)
    {
        $this->ordersRepository = $ordersRepository;
        $this->repository = $repository;
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws InvalidOrderRefundPaymentStatus
     */
    public function refund(array $payload): bool
    {
        /** @var Orders $order */
        $order = $this->ordersRepository->findOrFail($payload['order_id']);

        if ($payload['refund_amount'] > $this->calculateCanBeRefundAmount($order)) {
            throw new \Exception(__('orders.errors.amount_is_more_than_allowable'));
        }
        if ($order->payment_status != PaymentStatus::PAYMENT_STATUS_COMPLETED && $order->payment_status != PaymentStatus::PAYMENT_STATUS_REFUNDED_PARTIAL) {
            throw new InvalidOrderRefundPaymentStatus();
        }

        /** @var Refund $refund */
        $refund = $this->repository->create([
            'order_id' => $order->id,
            'amount' => $payload['refund_amount'],
            'reason' => $payload['reason'] ?? null,
        ]);

        if (getSetting(RefundSettings::ENABLE_MANUAL_REFUND) && $payload['is_manual']) {
            return $this->manualRefund($order, $refund);
        } else {
            if ($order->payment_type == PaymentType::INVOICE) {
                return $this->invoiceRefund($order, $refund);
            } else {
                $paymentMethod = PaymentProviderFactory::create($order->paymentMethod);

                return $paymentMethod->refund($order, $refund);
            }
        }
    }

    /**
     * @return false|string[]|null
     */
    public function getRefundReasons()
    {
        $reasons = getSetting('refund_reasons');

        $parsedReasons = null;

        if ($reasons) {
            $parsedReasons = explode("\n", $reasons);
        }

        return $parsedReasons;
    }

    /**
     * @param Orders $order
     * @param $refund
     * @return bool
     */
    private function manualRefund(Orders $order, $refund): bool
    {
        $order->update([
            'payment_status' => $order->amount > $order->refunded() ? PaymentStatus::PAYMENT_STATUS_REFUNDED_PARTIAL : PaymentStatus::PAYMENT_STATUS_REFUND,
        ]);

        $refund->update([
            'data' => __('orders.manual_refund'),
        ]);

        return true;
    }

    /**
     * @param Orders $order
     * @param Refund $refund
     * @return bool
     */
    private function invoiceRefund(Orders $order, Refund $refund): bool
    {
        $order->update([
            'payment_status' => $order->amount > $order->refunded() ? PaymentStatus::PAYMENT_STATUS_REFUNDED_PARTIAL : PaymentStatus::PAYMENT_STATUS_REFUND,
        ]);

        $refund->update([
            'data' => __('orders.refunds.refunded'),
        ]);

        return true;
    }

    /**
     * @param Orders $order
     * @return float|int
     */
    private function calculateCanBeRefundAmount(Orders $order)
    {
        return $order->amount - $order->refunded();
    }
}
