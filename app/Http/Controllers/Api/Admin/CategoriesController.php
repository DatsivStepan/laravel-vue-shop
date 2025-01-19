<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategory;
use App\Http\Resources\Admin\CategoriesCollection;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Categories;
use App\Repositories\Admin\CategoriesRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoriesController extends Controller
{
    private $repository;

    /**
     * @param CategoriesRepository $repository
     */
    public function __construct(CategoriesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        return new CategoriesCollection($this->repository->getWithQuery(
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
    public function show(int $id)
    {
        return new CategoryResource($this->repository->getOne($id, 'id', true));
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
     * @param Request $request
     * @return Response
     */
    public function reloadSortOrder(Request $request)
    {
        $requestData = $request->all();
        return [
            'status' => $this->repository->reloadSortOrder($requestData['items'], 'categories')
        ];
    }

    /**
     * @param int $id
     * @return Response
     */
    public function getParentCategories(int $id)
    {
        return $this->repository->getParentCategories($id);
    }

    /**
     * @param int $pageType
     * @return Response
     */
    public function getOptions(string $pageType)
    {
        if ($pageType == 'edit') {
            $parentOptions = $this->repository::getCategoriesInArray();
            $statusOptions = Categories::$statusOptions;

            return [
                'parentOptions' => FunctionHelper::arrayToVueOptions($parentOptions, true),
                'statusOptions' => FunctionHelper::arrayToVueOptions($statusOptions)
            ];
        }

        return [];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(CreateCategory $request, int $id)
    {
        $category = $this->repository->getOne($id);

        return new CategoryResource($this->repository->update($category, $request->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateCategory $request)
    {
        $request->validated();
        return $this->repository->create($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id)
    {
        return $this->repository->delete($this->repository->getOne($id));
    }
}
