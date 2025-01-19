<?php

namespace App\Http\Controllers\Api\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Sales\ShippingResource;
use App\Repositories\Admin\ShippingRepository;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateShipping;
use App\Http\Resources\Admin\Sales\ShippingsCollection;
use Exception;

class ShippingController extends Controller
{
    private $repository;

    /**
     * @param ShippingRepository $repository
     */
    public function __construct(ShippingRepository $repository)
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
        $user = auth()->user();
        if ($user && getSetting('enable_vendors') && getSetting('enable_vendors_filter') && ($vIds = $user->getManagerVendorsIds())) {
            $requestData = $request->all();
            $filters = isset($requestData['filter']) ? $requestData['filter'] : [];
            $filters['products_vendors'] = $vIds;
            $request->request->add(['filter' => $filters]);
        }

        return new ShippingsCollection($this->repository->getWithQuery(getDefaultPaginateCount()));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        return new ShippingResource($this->repository->getOne($id, 'order_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UpdateShipping $request
     * @return Response
     */
    public function store(UpdateShipping $request)
    {
        $request->validated();

        return $this->repository->create($request->all());
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
     * Update the specified resource in storage.
     *
     * @param UpdateShipping $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateShipping $request, int $id)
    {
        $consumer = $this->repository->getOne($id);

        return new ShippingResource($this->repository->update($consumer, $request->all()));
    }
}
