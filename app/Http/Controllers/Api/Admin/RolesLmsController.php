<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleLms;
use App\Repositories\Admin\RolesLmsRepository;
use App\Http\Resources\Admin\RolesLmsCollection;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RolesLmsController extends Controller
{
    private $repository;

    /**
     * @param RolesLmsRepository $repository
     */
    public function __construct(RolesLmsRepository $repository)
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
        return new RolesLmsCollection($this->repository->getWithQuery(
            getDefaultPaginateCount($this->repository->getTableName()))
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateRoleLms $request)
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
