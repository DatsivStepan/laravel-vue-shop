<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\VendorsUsersCollection;
use App\Models\VendorsUsers;
use App\Repositories\Admin\VendorsUsersRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendorsUsersController extends Controller
{
    private $repository;

    /**
     * @param VendorsUsersRepository $repository
     */
    public function __construct(VendorsUsersRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, $vendorId)
    {
        return new VendorsUsersCollection($this->repository->getWithPagination($vendorId, getDefaultPaginateCount()));
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
     * @param Request $request
     * @param string $action
     * @return Response
     */
    public function bulkActions(Request $request, string $action)
    {
        $requestData = $request->all();

        try {
            if (($ids = $requestData['ids']) && $action) {
                $status = false;
                switch ($action) {
                    case 'delete':
                        $status = $this->repository->deleteAll($ids);
                        break;
                }
            }

            return ['status' => $status ? true : false];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getUsers(Request $request)
    {
        $data = $request->validate([
            "vendor_id" => "required|integer",
            "type" => "required|integer",
            "query" => 'string'
        ]);
        $notUserIds = $this->repository->getAssignUserIds($data['vendor_id'], $data['type']);

        $query = isset($data['query']) ? $data['query'] : '';
        $users = $this->repository->getUsersInArray($query, $notUserIds);

        return FunctionHelper::arrayToVueOptions($users, true);
    }

    /**
     * @return Response
     */
    public function getTypes()
    {
        return FunctionHelper::arrayToVueOptions(VendorsUsers::getTypes(), true);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function assign(Request $request)
    {
        $data = $request->validate([
            "vendor_id" => "required|integer",
            "type" => "required|integer",
            "user_id" => "required"
        ]);

        return [
            'status' => $this->repository->assign(
                $data['vendor_id'],
                $data['user_id'],
                $data['type']
            )
        ];
    }
}
