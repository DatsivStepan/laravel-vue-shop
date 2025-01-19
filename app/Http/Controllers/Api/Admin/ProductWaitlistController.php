<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductWaitlistCollection;
use App\Http\Resources\Admin\ProductWaitlistResource;
use App\Models\ProductWaitlist;
use App\Repositories\Admin\ProductWaitlistRepository;
use App\Services\ProductWaitListService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductWaitlistController extends Controller
{
    /**
     * @var ProductWaitlistRepository
     */
    private $repository;

    /**
     * @var ProductWaitListService
     */
    private $service;

    /**
     * @param ProductWaitlistRepository $repository
     * @param ProductWaitListService $productWaitListService
     */
    public function __construct(ProductWaitlistRepository $repository, ProductWaitListService $productWaitListService)
    {
        $this->repository = $repository;
        $this->service = $productWaitListService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return new ProductWaitlistCollection($this->repository->getWithQuery(getDefaultPaginateCount()));
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
        return new ProductWaitlistResource($this->repository->getOne($id, 'id'));
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


    /**
     * @param ProductWaitlist $productWaitList
     */
    public function sendEmailToUser(ProductWaitlist $productWaitList)
    {
        $this->service->sendEmailToUser($productWaitList);
    }
}
