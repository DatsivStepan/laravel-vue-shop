<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PermissionsCollection;
use App\Repositories\Admin\PermissionsRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    private $repository;

    /**
     * @param PermissionsRepository $repository
     */
    public function __construct(PermissionsRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware(['auth:api', 'permission:manageRoles']);
    }

    /**
     * Display a listing of the resource.
     * @param int $roleId
     * @return Response
     */
    public function index(int $roleId)
    {
        $role = $this->findRole($roleId);

        $options = [
            'with' => [
                'roles' => function ($query) use ($roleId) {
                    $query->where('id', $roleId);
                }
            ]
        ];

        return new PermissionsCollection($this->repository->getWithPagination(getDefaultPaginateCount()), $role);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function changePermissionStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "roleId" => "required",
            "permissionId" => "required",
            "value" => "required",
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        return [
            'status' => $this->repository->changePermissionStatus(
                $request->get('roleId'),
                $request->get('permissionId'),
                (bool)$request->get('value')
            )
        ];
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

    /**
     * @param int $id
     * @return Response
     */
    protected function findRole(int $id)
    {
        if ($model = Role::findById($id)) {
            return $model;
        }

        throw new Exception('Cannot find model Role by ID: ' . $id);
    }
}
