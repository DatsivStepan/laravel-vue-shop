<?php

namespace App\Services;

use App;
use App\Classes\Enum\Order\BillingType;
use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Order\PaymentType;
use App\Events\Order\CompletedOrder;
use App\Events\Order\PendedOrder;
use App\Events\Order\RejectedOrder;
use App\Http\Resources\InstallmentOrderInCart;
use App\Http\Resources\OrderInCart;
use App\Models\Coupons;
use App\Models\Orders;
use App\Models\OrdersProductSeats;
use App\Models\Products;
use App\Notifications\OrderRejectedNotification;
use App\Repositories\Admin\CouponsRepository;
use App\Repositories\Admin\OrderProductSeatsRepository;
use App\Repositories\Admin\ProductsRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrdersRepository;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Payments\Entities\PaymentMethod;
use Modules\Payments\Repositories\PaymentMethodRepository;
use Modules\Payments\Services\PaymentMethods\Authorize;
use Modules\Payments\Services\PaymentMethods\Paypal;
use Modules\Payments\Services\PaymentMethods\Stripe;
use Throwable;

class OrderService
{
    /** @var OrdersRepository */
    protected $repository;
    /** @var ProductsRepository */
    protected $productRepository;
    /** @var CouponsRepository */
    protected $couponRepository;
    /** @var OrderProductSeatsRepository */
    protected $seatsRepository;
    /**
     * @var PaymentMethodRepository
     */
    private $paymentMethodRepository;

    public function __construct(
        OrdersRepository $repository,
        ProductsRepository $productRepository,
        CouponsRepository $couponRepository,
        PaymentMethodRepository $paymentMethodRepository,
        OrderProductSeatsRepository $seatsRepository
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->productRepository = $productRepository;
        $this->couponRepository = $couponRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->seatsRepository = $seatsRepository;
    }

    /**
     * @param int $userId
     * @param null $orderId
     *
     * @return Orders
     * @throws Exception
     *
     */
    public function getOrderByUser(int $userId, $orderId = null)
    {
        $with = ['products', 'coupons'];

        /** @var Orders $order */
        $order = $orderId ? $this->repository->findOrFail($orderId, $with) : $this->getOrderInCart($userId, false);

        return $order;
    }

    /**
     * @param int $userId
     * @param bool $orCreate
     *
     * @return Orders
     * @throws Exception
     *
     */
    public function getOrderInCart(int $userId, $orCreate = false)
    {
        $with = ['products', 'coupons'];
        $conditions = [
            'user_id' => $userId,
            'payment_status' => PaymentStatus::PAYMENT_STATUS_IN_CART,
        ];

        /** @var Orders $order */
        $order = $orCreate ? $this->repository->firstOrCreate($conditions, $with) :
            $this->repository->getOneByConditions($conditions, $with);

        return $order;
    }

    /**
     * @param Products $product
     * @param int $seats
     * @param array $result
     * @param null|int $userId
     *
     * @return array
     */
    protected function checkAvailableProductForAddToCart(Products $product, int $seats, array $result, $userId = null)
    {
        if (!$product) {
            return $result;
        }

        if ($product->enable_seats && ($availableSeats = (int)$product->getAvailableSeats($userId) < (int)$seats)) {
            $result['message'] = __('orders.errors.no_seats', ['seat_v' => $seats . '/' . $availableSeats]);

            return $result;
        }

        if (getSetting('enable_seats_vendors') && $product->enable_buy_ing_seats) {
            $countUserSould = $product->countSoldProducts(true, $userId);
            if ((int)$product->max_seats_per_user < ($seats) + $countUserSould) {
                $result['message'] = __('orders.errors.no_vendor_seats',
                    ['seat_v' => $seats . '/' . $product->max_seats_per_user, 'used' => $countUserSould]);

                return $result;
            }
        }
    }

    /**
     * @param Orders $order
     *
     * @return bool
     * @throws Throwable
     *
     */
    public function enrollUser(Orders $order)
    {
        return (new EnrollService())->enrollForOrder($order);
    }

    /**
     * @param Orders $order
     *
     * @return string
     * @throws Throwable
     *
     */
    public function generateInvoice(Orders $order)
    {
        $pdf = App::make('dompdf.wrapper');
        $view = view('pdf-templates.invoice',
            [
                'order' => $order,
                'products' => $order->products,
            ])->render();

        $pdf->loadHTML($view);

        $fileName = 'pdf/invoice_' . $order->id . '.pdf';

        Storage::put($fileName, $pdf->output());

        return base64_encode($fileName);
    }

    /**
     * @param int $orderId
     * @param string $key
     *
     * @throws Exception
     */
    public function usedSeats(int $orderId, string $key)
    {
        /** @var Orders $order */
        $order = $this->repository->findOrFail($orderId);
        /** @var OrdersProductSeats $pSeat */
        $pSeat = $this->seatsRepository->getOneByConditions(['seat_key' => $key]);
        if (!$pSeat) {
            return null;
        }

        if (!$pSeat->checkAvailability()) {
            throw new Exception(__('order.errors.seat_no_availability'));
        }

        $order->productSeatsUsed()->syncWithoutDetaching([
            $pSeat->id => ['status' => 1],
        ]);
    }

    /**
     * @param int $orderId
     * @param string $couponCode
     *
     * @throws Exception
     */
    public function addCoupon(int $orderId, string $couponCode)
    {
        /** @var Orders $order */
        $order = $this->repository->findOrFail($orderId);
        /** @var Coupons $coupon */
        $coupon = $this->couponRepository->findByCode($couponCode);
        if (!$coupon) {
            return null;
        }

        if ($order->coupons()->whereKey($coupon->id)->exists()) {
            throw new Exception(__('order.errors.coupon_already_added'));
        }

        $order->coupons()->syncWithoutDetaching([
            $coupon->id => ['coupon_code' => $coupon->code],
        ]);
    }

    /**
     * @param int $orderId
     * @param int $couponId
     *
     * @throws Exception
     */
    public function deleteCoupon(int $orderId, int $couponId)
    {
        /** @var Orders $order */
        $order = $this->repository->findOrFail($orderId);

        if (!$order->coupons()->whereKey($couponId)->exists()) {
            throw new Exception(__('order.errors.has_not_coupon'));
        }

        $order->coupons()->detach($couponId);
    }

    /**
     * @param int $orderId
     * @param int $seatUseId
     *
     * @throws Exception
     */
    public function deleteSeatUse(int $orderId, int $seatUseId)
    {
        /** @var Orders $order */
        $order = $this->repository->findOrFail($orderId);

        if (!$order->productSeatsUsed()->where('order_product_seats_used.id', $seatUseId)->exists()) {
            throw new Exception(__('order.errors.has_not_use_seat'));
        }

        $order->productSeatsUsed()->where('order_product_seats_used.id', $seatUseId)->detach();
    }

    /**
     * @param int $userId
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function addProductToCart(int $userId, array $data)
    {
        $seats = $data['seats'];
        $productId = $data['id'];
        $result = [
            'status' => false,
            'message' => __('orders.errors.no_product'),
        ];

        $order = $this->getOrderInCart($userId, true);

        if ($data['delete_other']) {
            $order->products()->detach();
        }

        /** @var Products $product */
        $product = $this->productRepository->find($productId);

        if ($err = $this->checkAvailableProductForAddToCart($product, $seats, $result, $userId)) {
            return $err;
        }

        if ($this->repository->isOrderHasProduct($order->id, $productId)) {
            $result['message'] = __('orders.errors.product_already_added');

            return $result;
        }

        $productOrderData = [
            $product->id => [
                'quantity' => $seats,
            ],
        ];

        $this->repository->addProducts($order, $productOrderData);
        $this->repository->addDiscountFromProductToOrder($order, $product);

        return [
            'status' => true,
            'message' => __('orders.request_messages.added_to_cart'),
        ];
    }

    /**
     * @param int $orderId
     * @param int $productId
     * @param int $quantity
     * @return bool
     * @throws Exception
     */
    public function deleteProductFromCart(int $orderId, int $productId, int $quantity = 1)
    {
        /** @var Orders $order */
        $order = $this->repository->findOrFail($orderId);

        $productQuantity = OrderProductRepository::getProductQuantity($orderId, $productId);
        if ($productQuantity == 0) {
            throw new Exception(__('orders.errors.no_product'));
        }

        DB::transaction(function () use ($order, $productId, $quantity, $productQuantity) {
            if ($newQuantity = $productQuantity - $quantity) {
                $order->products()->updateExistingPivot($productId,
                    [
                        'quantity' => $newQuantity,
                    ]);
            } else {
                $order->products()->detach($productId);
            }

            if ($this->isOrderEmpty($order)) {
                $this->repository->delete($order);
            } else {
                $this->repository->deleteEmptyDiscounts($order);

                $this->recalculate($order);
            }
        });


        return true;
    }

    /**
     * @param Orders $order
     * @throws Throwable
     */
    private function recalculate(Orders $order)
    {
        if (
            $order->payment_status === PaymentStatus::PAYMENT_STATUS_PENDING &&
            $order->payment_type === PaymentType::INVOICE &&
            $order->billing_type === BillingType::REGULAR
        ) {
            $this->pendingOrder($order->id, ['payment_method_id' => null]);
        }
    }

    /**
     * @param Orders $order
     *
     * @return bool
     * @throws Exception
     *
     */
    public function isOrderEmpty(Orders $order)
    {
        return $order->products()->doesntExist();
    }

    /**
     * @param int $orderId
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public function pendingOrder(int $orderId, $data = []): array
    {
        /** @var Orders $order */
        $order = $this->repository->findOrFail($orderId);

        $billingType = $data['billing_type'] ?? BillingType::REGULAR;
        $orderData = $this->getOrderData($order, $billingType);

        $this->saveOrderProduct($order, $orderData['products']);
        $this->saveOrderDiscount($order);

        $modifyData = $this->prepareOrderData($order, $data, $orderData);
        $order->fill($modifyData);
        $order->save();

        $order->products = $orderData['products'];

        event(new PendedOrder($order));
        //$order->user->notify(new OrderCompetedNotification($order));

        $paymentMethod = $this->paymentMethodRepository->find($data['payment_method_id']);
        $options = $this->paymentTypeOperations($order, $paymentMethod, $data['extra_data'] ?? null);

        return [
            'status' => true,
            'message' => __('orders.payed'),
            'options' => $options,
        ];
    }

    /**
     * @param Orders $order
     * @param array $data
     * @param array $orderData
     * @return array
     */
    private function prepareOrderData(Orders $order, array $data, array $orderData): array
    {
        $modifyData = $data;

        $modifyData['subtotal'] = $orderData['subtotal'];
        $modifyData['amount'] = $orderData['total'];
        $modifyData['tax'] = $orderData['tax_amount'];
        $modifyData['currency'] = $orderData['currency'];
        $modifyData['base_currency'] = getSetting('currency');
        $modifyData['rate'] = currency(1, $modifyData['currency'], $modifyData['base_currency'], false);
        $modifyData['discount'] = $orderData['discount'] ?? 0;
        $modifyData['note'] = $order->note . join(' | ', $order->products()->pluck('products.name')->toArray());
        $modifyData['payment_status'] = PaymentStatus::PAYMENT_STATUS_PENDING;

        return $modifyData;
    }

    /**
     * @param Orders $order
     * @param PaymentMethod|null $paymentMethod
     * @param null $extraData
     * @return array
     * @throws Throwable
     */
    private function paymentTypeOperations(Orders $order, PaymentMethod $paymentMethod = null, $extraData = null): array
    {
        $options = ['order_id' => $order->id];

        switch ($order->payment_type) {
            case PaymentType::INVOICE:
                break;
            case PaymentType::PAYPAL:
                if ($paymentMethod) {
                    $paypal = new PayPal($paymentMethod);
                    $paypalOrder = $paypal->makeOrder($order, $extraData);

                    $options['payment_type'] = PaymentType::PAYPAL;
                    $options['paypal'] = $paypalOrder;

                    $this->repository->updateIn([$order->id], [
                        'payment_method_id' => $paymentMethod->id,
                        'external_id' => $paypalOrder['id'],
                    ]);
                }
                break;
            case PaymentType::STRIPE:
                if ($paymentMethod) {
                    $stripe = new Stripe($paymentMethod);
                    $stripeSession = $stripe->makeOrder($order, $extraData);

                    $options['payment_type'] = PaymentType::STRIPE;
                    $options['stripe'] = $stripeSession;

                    $this->repository->updateIn([$order->id], [
                        'payment_method_id' => $paymentMethod->id,
                        'external_id' => $stripeSession['sessionId'],
                    ]);
                }
                break;
            case PaymentType::AUTHORIZE:
                if ($paymentMethod) {
                    $authorize = new Authorize($paymentMethod);

                    $authorizeData = $authorize->makeOrder($order, $extraData);

                    $options['payment_type'] = PaymentType::AUTHORIZE;
                    $options['authorize'] = $authorizeData;

                    $paymentData = App\Models\PaymentData::query()->create([
                        'order_id' => $order->id,
                        'data' => $authorizeData,
                    ]);

                    if ($authorizeData['status']) {
                        $this->repository->updateIn([$order->id], [
                            'payment_data_id' => $paymentData->id,
                            'payment_method_id' => $paymentMethod->id,
                            'external_id' => $authorizeData['transaction_id'],
                        ]);

                        $order = $this->repository->find($order->id);
                        $this->complete($order);
                    }
                }
                break;
        }

        return $options;
    }

    /**
     * @param Orders $order
     * @param string $billingType
     * @return array
     */
    private function getOrderData(Orders $order, string $billingType): array
    {
        switch ($billingType) {
            case BillingType::INSTALLMENT:
                return (new InstallmentOrderInCart($order))->toArray(request());
            default:
                return (new OrderInCart($order))->toArray(request());
        }
    }

    protected function saveOrderProduct(Orders $order, array $products)
    {
        foreach ($products as $product) {

            $attributes = Arr::only($product,
                ['price', 'discount', 'discount_price', 'name', 'quantity', 'tax']
            );

            $order->products()->updateExistingPivot($product['id'], $attributes);
        }
    }

    public function createSeats(Orders $order)
    {
        $orderProducts = $order->orderProducts();
        if ($orderProducts->count()) {
            (new OrderProductSeatsRepository())->createdSeats($orderProducts);
        }
    }

    public function complete(Orders $order, array $data = [])
    {
        $order->refresh();
        $data['payment_status'] = PaymentStatus::PAYMENT_STATUS_COMPLETED;
        $order->update($data);

        $this->enrollUser($order);
        if (getSetting('enable_seats_vendors') == 1) {
            $this->createSeats($order);
        }

        event(new CompletedOrder($order));
    }

    protected function saveOrderDiscount(Orders $order)
    {
        foreach ($order->discounts as $discount) {
            $order->discounts()->updateExistingPivot($discount->id,
                [
                    'discount_name' => $discount->name,
                    'discount' => $discount->discount,
                    'product_count' => $this->repository->getProductsWithDiscount($order, $discount->id),
                ]);
        }
    }

    /**
     * @param Orders $order
     * @return array
     * @throws Exception
     */
    public function approval(Orders $order): array
    {
        $this->checkEnableManualInvoiceApproval($order);
        $this->checkPendingPaymentStatus($order);

        if ($order->payment_type !== PaymentType::INVOICE) {
            $updatedData = [
                'payment_type' => PaymentType::INVOICE,
            ];

            /** @var Orders $order */
            $order = $this->repository->update($order, $updatedData);
        }

        $this->complete($order);

        return [
            'status' => true,
            'message' => __('orders.response_messages.payed'),
        ];
    }

    /**
     * @param Orders $order
     * @throws Exception
     */
    public function reject(Orders $order): array
    {
        $this->checkEnableManualInvoiceApproval($order);
        $this->checkPendingPaymentStatus($order);

        $updatedData = [
            'payment_status' => PaymentStatus::PAYMENT_STATUS_REJECTED,
        ];

        /** @var Orders $order */
        $order = $this->repository->update($order, $updatedData);

        event(new RejectedOrder($order));

        $order->user->notify(new OrderRejectedNotification($order));

        return [
            'status' => true,
            'message' => __('orders.response_messages.rejected'),
        ];
    }

    /**
     * @param Orders $order
     * @throws Exception
     */
    private function checkEnableManualInvoiceApproval(Orders $order)
    {
        if (!getSetting('enable_manual_invoices_approval') && $order->payment_type !== PaymentType::INVOICE) {
            throw new Exception(__('errors.settings.sales.enable_manual_invoices_approval.disabled'));
        }
    }

    /**
     * @param Orders $order
     * @throws Exception
     */
    private function checkPendingPaymentStatus(Orders $order)
    {
        if (!$order->isPending()) {
            throw new Exception(__('errors.orders.manual_approval.status_no_pending'));
        }
    }
}
