<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\VendorsAssignCollection;
use App\Repositories\Admin\VendorsAssignRepository;
use App\Repositories\Admin\VendorsRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendorsAssignController extends Controller
{
    private $repository;

    /**
     * @param VendorsAssignRepository $repository
     */
    public function __construct(VendorsAssignRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $resource = new VendorsAssignCollection($this->repository->getWithQuery(getDefaultPaginateCount()));

        return $resource->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->repository->delete($this->repository->getOne($id));

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @param string $action
     * @return Response
     * @throws Exception
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
     * @return JsonResponse
     */
    public function getVendorsOptions(Request $request): JsonResponse
    {
        $requestData = $request->all();

        $vendors = VendorsRepository::getVendorsInArray($requestData['instanceType'], $requestData['instanceId']);

        return response()->json(FunctionHelper::arrayToVueOptions($vendors, true));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function assign(Request $request): JsonResponse
    {
        $data = [
            'status' => $this->repository->assign($request->all()),
        ];

        return response()->json($data);
    }
}
