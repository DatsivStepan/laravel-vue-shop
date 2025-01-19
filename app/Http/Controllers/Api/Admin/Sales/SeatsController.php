<?php

namespace App\Http\Controllers\Api\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Sales\SeatResource;
use App\Repositories\Admin\ShippingRepository;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Resources\Admin\Sales\SeatsCollection;
use App\Http\Resources\Admin\Sales\SeatsDetailsCollection;
use App\Repositories\Admin\Sales\SeatsDetailsRepository;
use App\Repositories\Admin\Sales\SeatsRepository;
use Exception;

class SeatsController extends Controller
{
    private $repository;

    /**
     * @param ShippingRepository $repository
     * @param SeatsDetailsRepository $seatsDetailsRepository
     */
    public function __construct(SeatsRepository $repository, SeatsDetailsRepository $seatsDetailsRepository)
    {
        $this->repository = $repository;
        $this->detailsRepository = $seatsDetailsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return new SeatsCollection($this->repository->getWithQuery(getDefaultPaginateCount()));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        return new SeatResource($this->repository->getOne($id));
    }

    /**
     * Display a listing of the resource.
     * @param int $id
     *
     * @return Response
     */
    public function details(Request $request, int $id)
    {
        return new SeatsDetailsCollection($this->detailsRepository->getWithQuery($id, getDefaultPaginateCount()));
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
