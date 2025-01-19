<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Enum\ModelStatus;
use App\Events\Discount\Deleted;
use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Discounts\Store;
use App\Http\Requests\Discounts\Update;
use App\Http\Resources\Admin\Discounts\DiscountAssignProductCollection;
use App\Http\Resources\Admin\Discounts\DiscountCollection;
use App\Http\Resources\Admin\Discounts\DiscountResource;
use App\Models\Discount;
use App\Repositories\Admin\DiscountRepository;
use App\Services\Admin\DiscountService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DiscountController extends Controller
{
    /** @var DiscountService $service */
    private $service;
    /** @var DiscountRepository $repository */
    private $repository;

    public function __construct(DiscountRepository $repository, DiscountService $service)
    {
        $this->repository = $repository;
        $this->service = $service;

        $this->middleware(['permission:manageDiscounts']);
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
        $discounts = $this->repository->getWithQuery($user,
            getDefaultPaginateCount($this->repository->getTableName()));
        $result = new DiscountCollection($discounts, auth()->user());

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
        $discount = $this->repository->getOneOrFail($id, 'id');

        $result = new DiscountResource($discount);

        return $result->response()->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update $request
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|JsonResponse
     * @throws Exception
     */
    public function update(Update $request, int $id)
    {
        $data = $request->validated();
        /** @var Discount $discount */
        $discount = $this->repository->getOneOrFail($id);

        $result = new DiscountResource($this->repository->update($discount, $data));

        return $result->response()->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Store $request
     *
     * @return \Illuminate\Http\JsonResponse|Response|JsonResponse
     * @throws Exception
     *
     */
    public function store(Store $request)
    {
        $data = $request->validated();

        $discount = $this->repository->create($data);
        $result = new DiscountResource($discount);

        return $result->response()->setEncodingOptions(JSON_NUMERIC_CHECK);
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
            'ids' => 'required|array',
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
        $discount = $this->repository->getOneOrFail($id);

        if ($model = $this->repository->delete($discount)) {
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
            'discount_id' => 'required|exists:discounts,id',
            'products' => 'array',
            'products.*.id' => 'integer',
            'assign_all' => 'boolean',
        ]);

        $data['products'] = $data['products'] ?? [];
        $data['assign_all'] = $data['assign_all'] ?? false;

        $discount = $this->service->assignProducts($data['discount_id'], $data['products'], $data['assign_all']);

        return response()->json($discount);
    }

    /**
     * @param Request $request
     */
    public function detachProduct(Request $request)
    {
        $data = $request->validate([
            'discount_id' => 'required|exists:discounts,id',
            'product_id' => 'required',
        ]);

        $this->service->detachProduct($data['discount_id'], $data['product_id']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignVendors(Request $request)
    {
        $data = $request->validate([
            'discount_id' => 'required|exists:discounts,id',
            'vendors' => 'array',
            'vendors.*.id' => 'integer',
            'assign_all' => 'boolean',
        ]);

        $data['vendors'] = $data['vendors'] ?? [];
        $data['assign_all'] = $data['assign_all'] ?? false;

        $coupon = $this->service->assignVendors($data['discount_id'], $data['vendors'], $data['assign_all']);

        return response()->json($coupon);
    }

    /**
     * @param string $pageType
     *
     * @return \Illuminate\Http\JsonResponse|Response|JsonResponse
     */
    public function getOptions(string $pageType)
    {
        if ($pageType == 'edit') {
            $statusOptions = Discount::getStatusOptions();
            $typeOptions = Discount::getTypeOptions();

            $options = [
                'statusOptions' => FunctionHelper::arrayToVueOptions($statusOptions),
                'typeOptions' => FunctionHelper::arrayToVueOptions($typeOptions),
            ];

            return response()->json($options)->setEncodingOptions(JSON_NUMERIC_CHECK);
        }

        return response()->json();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductForSelect(Request $request)
    {
        $request->validate([
            'discount_id' => 'required|exists:discounts,id',
        ]);

        $products = $this->service->getProductForSelect($request->discount_id);

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
     * @return DiscountAssignProductCollection
     */
    public function getAssignedProducts(Request $request)
    {
        $data = $request->validate([
            'discount_id' => 'required|exists:discounts,id',
            'filters' => 'nullable|string',
            'sort' => 'nullable|string',
        ]);

        $data['filters'] = isset($data['filters']) ? json_decode($data['filters'], true) : [];

        $products = $this->service->getAssignedProducts($data['discount_id'],
            getDefaultPaginateCount(),
            $data['filters']);

        return new DiscountAssignProductCollection($products, $data['discount_id']);
    }
}
