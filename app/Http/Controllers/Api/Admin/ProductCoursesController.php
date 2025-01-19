<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductCoursesCollection;
use App\Http\Resources\Admin\ProductCoursesResource;
use App\Repositories\Admin\CourseCategoriesRepository;
use App\Repositories\Admin\CourseRepository;
use App\Repositories\Admin\ProductCoursesRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductCoursesController extends Controller
{
    private $repository;

    /**
     * @param ProductCoursesRepository $repository
     */
    public function __construct(ProductCoursesRepository $repository)
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
        return new ProductCoursesCollection($this->repository->getWithQuery(getDefaultPaginateCount()));
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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        return $this->repository->create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return new ProductCoursesResource($this->repository->getOne($id, 'id'));
    }

    /**
     * @return Response
     */
    public function getCategories()
    {
        $categories = (new CourseCategoriesRepository)->get();
        return FunctionHelper::modelsToVueOptions($categories, 'id', 'name', true);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getCourses(Request $request)
    {
        $query = $request->get("query");
        $productId = $request->get('product_id');
        $courses = (new CourseRepository)
            ->getCoursesForSelect(true, $request->get('category_id'), $productId ? [$productId] : [], $query);

        return FunctionHelper::modelsToVueOptions($courses, 'id', 'name', true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        return $this->repository->delete($this->repository->getOne($id));
    }
}
