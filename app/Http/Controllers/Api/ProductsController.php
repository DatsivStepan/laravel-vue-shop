<?php

namespace App\Http\Controllers\Api;

use App\Facades\UserSettings;
use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductCollection;
use App\Http\Resources\Admin\ProductResource;
use App\Repositories\Admin\CustomFieldsRepository;
use App\Repositories\Admin\ProductsRepository;
use App\Repositories\Admin\UserRepository;
use App\Repositories\Admin\VendorsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    private $repository;

    /**
     * @param ProductsRepository $repository
     */
    public function __construct(ProductsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
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
     * @param Request $request
     * @return Response
     */
    public function getCustomFieldsArray(Request $request)
    {
        $options = [
            'search' => $request->get('query')
        ];

        return FunctionHelper::modelsToVueOptions((new CustomFieldsRepository)
            ->get($options), 'id', 'title', true);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getUsersArray(Request $request)
    {
        $options = [
            'search' => $request->get('query')
        ];

        return FunctionHelper::modelsToVueOptions((new UserRepository)
            ->get($options), 'id', 'name', true);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getVendorsArray(Request $request)
    {
        $options = [
            'search' => $request->get('query')
        ];

        return FunctionHelper::modelsToVueOptions((new VendorsRepository)
            ->get($options), 'id', 'name', true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getFeatured(Request $request)
    {
        $options = [
            'sort' => [
                'field' => 'sortorder',
                'direction' => 'desc',
            ],
            'filters' => [
                'featured' => 1,
            ],
        ];

        return new ProductCollection($this->repository->get($options), Auth::user());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        return (new ProductResource($this->repository->getOne($id),
            UserSettings::getCurrency(Auth::user())))->response();
    }
}
