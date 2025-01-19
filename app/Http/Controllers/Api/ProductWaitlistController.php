<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductWaitlistCollection;
use App\Repositories\Admin\ProductWaitlistRepository;
use App\Services\ProductWaitListService;
use App\Services\RequestService;
use Exception;
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
        (new RequestService($request))->addToFilter('user_id', auth()->id());

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
    public function addToWaitlist(Request $request, int $productId)
    {
        $status = $this->repository->addProductToWaitlist($productId, auth()->user()->id);
        return [
            'status' => $status,
            'message' => $status ? __('waitlist.product_added_to_waitlist') : __('waitlist.error_added_to_waitlist')
        ];
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
