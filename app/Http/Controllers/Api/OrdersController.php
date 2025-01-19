<?php

namespace App\Http\Controllers\Api;

use App\Classes\Enum\Order\BillingType;
use App\Classes\Enum\Order\PaymentType;
use App\Facades\UserSettings;
use App\Http\Controllers\Controller;
use App\Http\Resources\InstallmentOrderInCart;
use App\Http\Resources\OrderInCart;
use App\Http\Resources\OrdersCollection;
use App\Repositories\OrdersRepository;
use App\Rules\CouponSeatsExists;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /** @var OrdersRepository $repository */
    private $repository;

    /** @var OrderService $service */
    private $service;

    /**
     * @param OrdersRepository $repository
     * @param OrderService $service
     */
    public function __construct(OrdersRepository $repository, OrderService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $user = auth()->user();
        return (new OrdersCollection($this->repository->getWithQuery($user,
            getDefaultPaginateCount($this->repository->getTableName()))))->response();
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getOrderData(Request $request)
    {
        $request->validate([
            'billing_type' => 'string',
            'order_id' => 'integer',
        ]);

        $order = $this->service->getOrderByUser(auth()->id(), $request->order_id);

        if (request('billing_type') === BillingType::INSTALLMENT &&
            $order->products->count() == 1 &&
            $order->products->first()->billing_type == BillingType::INSTALLMENT) {
            $resource = new InstallmentOrderInCart($order);
        } else {
            $resource = new OrderInCart($order);
        }

        return response()->json($resource);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getPaymentMethods(): JsonResponse
    {
        $user = Auth::user();
        $userCurrency = UserSettings::getCurrency($user);
        $data = [
            'payments' => $this->repository->getPaymentMethods($userCurrency),
        ];

        return response()->json($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     *
     */
    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'seats' => 'required',
            'delete_other' => 'nullable',
        ]);

        $responseData = $this->service->addProductToCart(Auth::id(), $data);

        return response()->json($responseData);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function paymentInvoice(Request $request)
    {
        $requestData = $request->validate([
            'order_id' => 'required',
            'payment_method_id' => 'nullable',
            'payment_type' => 'nullable',
        ]);

        $params = [
            'payment_type' => request('payment_type', PaymentType::INVOICE),
            'payment_method_id' => request('payment_method_id'),
            'billing_type' => request('billing_type', BillingType::REGULAR),
            'extra_data' => request('extra_data'),
        ];

        $responseData = $this->service->pendingOrder($requestData['order_id'], $params);
        return response()->json($responseData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function removeFromCart(Request $request)
    {
        $payload = $request->validate([
            'order_id' => 'required',
            'product_id' => 'required_without:products',
            'products' => 'required_without:product_id',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required',
        ]);

        if (isset($payload['products'])) {
            $products = $payload['products'];
        } else {
            $products = [
                [
                    'id' => $payload['product_id'],
                    'quantity' => isset($request['quantity']) ? $request['quantity'] : 1,
                ],
            ];
        }

        $responseData = false;

        foreach ($products as $product) {
            $responseData = $this->service->deleteProductFromCart($payload['order_id'], $product['id'],
                $product['quantity']);
        }

        return response()->json($responseData);
    }

    public function addCoupon(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'coupon_code' => ['required', new CouponSeatsExists()],
        ]);

        $this->service->usedSeats($data['order_id'], $data['coupon_code']);

        $this->service->addCoupon($data['order_id'], $data['coupon_code']);
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     */
    public function deleteOrder(int $id)
    {
        return $this->repository->delete($this->repository->getOne($id));
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     */
    public function deleteCoupon(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'coupon_id' => 'required|exists:coupons,id',
        ]);

        $this->service->deleteCoupon($data['order_id'], $data['coupon_id']);
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     */
    public function deleteSeatUse(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'seat_use_id' => 'required|exists:coupons,id',
        ]);

        $this->service->deleteSeatUse($data['order_id'], $data['seat_use_id']);
    }
}
