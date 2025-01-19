<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Enum\Subscriptions\SubscriptionAction;
use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProduct;
use App\Http\Requests\UpdateProduct;
use App\Http\Resources\Admin\ProductCollection;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Categories;
use App\Models\Products;
use App\Repositories\Admin\ProductsRepository;
use App\Repositories\UserRepository;
use App\Services\Admin\ProductService;
use App\Services\ThemeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductsController extends Controller
{
    /** @var ProductsRepository $repository */
    private $repository;

    /** @var UserRepository $userRepository */
    private $userRepository;
    /**
     * @var ProductService
     */
    private $service;

    /**
     * @param ProductsRepository $repository
     * @param UserRepository $userRepository
     * @param ProductService $service
     */
    public function __construct(ProductsRepository $repository, UserRepository $userRepository, ProductService $service)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        return new ProductCollection($this->repository->getWithQuery(
                $user,
                getDefaultPaginateCount($this->repository->getTableName())
            ), $user);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return new ProductResource($this->repository->getOne($id, 'id'));
    }

    /**
     * @param Request $request
     * @param string $action
     * @return Response
     */
    public function bulkActions(Request $request, string $action)
    {
        $data = $request->validate([
            "ids" => "required|array",
        ]);

        if (method_exists($this->repository, $action)) {
            return [
                'status' => $this->repository->{$action}($data['ids'])
            ];
        }

        throw new Exception("Action '" . $action . "' not exist");
    }

    /**
     * @param Request $request
     * @param int $productId
     * @return Response
     */
    public function actions(Request $request, int $productId)
    {
        $data = $request->validate([
            "action" => "required|string",
        ]);

        if (method_exists($this->repository, $data['action'])) {
            return [
                'status' => $this->repository->{$data['action']}($productId)
            ];
        }

        throw new Exception("Action '" . $data['action'] . "' not exist");
    }

    /**
     * @param int $pageType
     * @return array
     */
    public function getOptions($pageType)
    {
        if ($pageType == 'edit') {
            return [
                'statusOptions' => FunctionHelper::arrayToVueOptions(Products::$statusOptions),
                'showItemsOptions' => FunctionHelper::arrayToVueOptions(getDefaultSelectOptions()),
                'recurringPeriods' => FunctionHelper::arrayToVueOptions(getDatesPeriods()),
                'subscriptionActions' => FunctionHelper::arrayToVueOptions(SubscriptionAction::getOptions()),
                'categories' => Categories::getListInArray(),
                'enroll' => [
                    'end_types' => FunctionHelper::arrayToVueOptions(Products::getEnrolEndTypes()),
                    'on_types' => FunctionHelper::arrayToVueOptions(Products::getEnrolOnTypes()),
                    'start_types' => FunctionHelper::arrayToVueOptions(Products::getEnrolStartTypes()),
                ],
                // 'instructors' => FunctionHelper::modelsToVueOptions($this->userRepository->get(), 'id', 'name'),
                'themes' => FunctionHelper::arrayToVueOptions(ThemeService::getThemes()),
            ];
        } else {
            if ($pageType == 'categories') {
                return Categories::getListInArray();
            } else {
                if ($pageType == 'instructors') {
                    return FunctionHelper::modelsToVueOptions($this->userRepository->get(), 'id', 'name');
                } else {
                    if ($pageType == 'filters') {
                        return [
                            'instructors' => FunctionHelper::modelsToVueOptions($this->userRepository->get(), 'id',
                                'name')
                        ];
                    }
                }
            }
        }

        return [];
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function reloadSortOrder(Request $request)
    {
        $requestData = $request->all();
        return [
            'status' => $this->repository->reloadSortOrder($requestData['items'], 'products')
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\CreateProduct $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateProduct $request, $id)
    {
        $product = $this->repository->getOne($id);

        $product = $this->service->update($product, $request->all());
        return new ProductResource($product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\CreateProduct $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function store(CreateProduct $request): \Illuminate\Http\JsonResponse
    {
        $request->validated();

        return response()->json($this->service->create($request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $product = $this->repository->getOne($id);

        return $this->repository->delete($product);
    }
}
