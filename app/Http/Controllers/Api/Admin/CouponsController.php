<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Enum\ModelStatus;
use App\Events\Coupons\Deleted;
use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCoupon;
use App\Http\Requests\UpdateCoupon;
use App\Http\Resources\Admin\CouponResource;
use App\Http\Resources\Admin\Coupons\CouponsAssignProductCollection;
use App\Http\Resources\Admin\CouponsCollection;
use App\Models\Coupons;
use App\Repositories\Admin\CouponsRepository;
use App\Services\Admin\CouponService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CouponsController extends Controller
{
    /** @var CouponsRepository $repository */
    private $repository;
    /** @var CouponService $service */
    private $service;

    /**
     * @param CouponsRepository $repository
     * @param CouponService $service
     */
    public function __construct(CouponsRepository $repository, CouponService $service)
    {
        $this->repository = $repository;
        $this->service = $service;

        $this->middleware(['permission:manageCoupons']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|Response|JsonResponse
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $result = new CouponsCollection($this->repository->getWithQuery($user,
            getDefaultPaginateCount($this->repository->getTableName())), $user);
        return $result->response()->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|JsonResponse
     */
    public function show(int $id)
    {
        $coupon = $this->repository->getOne($id, 'id');

        if (is_null($coupon)) {
            abort(404);
        }

        $result = new CouponResource($coupon);

        return $result->response()->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @param string $pageType
     *
     * @return \Illuminate\Http\JsonResponse|Response|JsonResponse
     */
    public function getOptions(string $pageType)
    {
        if ($pageType == 'edit') {
            $statusOptions = Coupons::getStatusOptions();

            $options = [
                'statusOptions' => FunctionHelper::arrayToVueOptions($statusOptions),
            ];

            return response()->json($options)->setEncodingOptions(JSON_NUMERIC_CHECK);
        }

        return response()->json();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCoupon $request
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|JsonResponse
     * @throws Exception
     */
    public function update(UpdateCoupon $request, int $id)
    {
        $params = $request->validated();

        /** @var Coupons $coupon */
        $coupon = $this->repository->getOne($id);

        if (is_null($coupon)) {
            abort(404);
        }

        $result = new CouponResource($this->repository->update($coupon, $params));

        return $result->response()->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCoupon $request
     *
     * @return \Illuminate\Http\JsonResponse|Response|JsonResponse
     * @throws Exception
     *
     */
    public function store(StoreCoupon $request)
    {
        $data = $request->validated();

        $coupon = $this->service->create($data);
        $result = new CouponResource($coupon);

        return $result->response()->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     *
     */
    public function destroy(int $id)
    {
        $coupon = $this->repository->getOne($id);

        if (is_null($coupon)) {
            abort(404);
        }

        if ($model = $this->repository->delete($coupon)) {
            // Trigger event.
            Deleted::dispatch($model);
        }

        return response()->json($model);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignProducts(Request $request)
    {
        $data = $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'products' => 'array',
            'products.*.id' => 'integer',
            'assign_all' => 'boolean'
        ]);

        $data['products'] = $data['products'] ?? [];
        $data['assign_all'] = $data['assign_all'] ?? false;

        $coupon = $this->service->assignProducts($data['coupon_id'], $data['products'], $data['assign_all']);

        return response()->json($coupon);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignVendors(Request $request)
    {
        $data = $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'vendors' => 'array',
            'vendors.*.id' => 'integer',
            'assign_all' => 'boolean'
        ]);

        $data['vendors'] = $data['vendors'] ?? [];
        $data['assign_all'] = $data['assign_all'] ?? false;

        $coupon = $this->service->assignVendors($data['coupon_id'], $data['vendors'], $data['assign_all']);

        return response()->json($coupon);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductForSelect(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
        ]);

        $products = $this->service->getProductForSelect($request->coupon_id);

        $result = FunctionHelper::modelsToVueOptions(
            $products,
            'id',
            'name',
            true
        );

        return response()->json($result);
    }

    /**
     * @param Request $request
     *
     * @return CouponsAssignProductCollection
     */
    public function getAssignedProducts(Request $request)
    {
        $data = $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'filters' => 'nullable|string',
            'sort' => 'nullable|string',
        ]);

        $data['filters'] = isset($data['filters']) ? json_decode($data['filters'], true) : [];
        $products = $this->service->getAssignedProducts($data['coupon_id'], getDefaultPaginateCount(),
            $data['filters']);

        return new CouponsAssignProductCollection($products, $data['coupon_id']);
    }

    /**
     * @param Request $request
     */
    public function detachProduct(Request $request)
    {
        $data = $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'product_id' => 'required'
        ]);

        $this->service->detachProduct($data['coupon_id'], $data['product_id']);
    }


    /**
     * @param Request $request
     * @param string $action
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     *
     */
    public function bulkActions(Request $request, string $action)
    {
        $data = $request->validate([
            'ids' => 'required|array'
        ]);

        try {
            $status = false;
            switch ($action) {
                case 'active':
                    $status = $this->repository->updateIn($data['ids'], ['status' => ModelStatus::ACTIVE]);
                    break;

                case 'inactive':
                    $status = $this->repository->updateIn($data['ids'], ['status' => ModelStatus::INACTIVE]);
                    break;

                case 'delete':
                    $status = $this->repository->deleteIn($data['ids']);
                    break;
            }

            return response()->json(['status' => $status]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
