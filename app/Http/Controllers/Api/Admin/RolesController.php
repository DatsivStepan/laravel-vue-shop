<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RoleCollection;
use App\Http\Resources\Admin\RoleResource;
use App\Repositories\Admin\RolesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RolesController extends Controller
{
    private $repository;

    /**
     * @param RolesRepository $repository
     */
    public function __construct(RolesRepository $repository)
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
        $requestData = $request->all();
        $options = [];
        if (!empty($requestData['sort'])) {
            $sort = json_decode($requestData['sort']);

            $options['sort'] = [
                'field' => $sort->column,
                'direction' => $sort->reverse ? 'desc' : 'asc'
            ];
        }

        return new RoleCollection($this->repository->paginate(getDefaultPaginateCount()), $options);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return new RoleResource($this->repository->getOne($id, 'id', true));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        $category = $this->repository->getOne($id);

        return new RoleResource($this->repository->update($category, $request->all()));
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
