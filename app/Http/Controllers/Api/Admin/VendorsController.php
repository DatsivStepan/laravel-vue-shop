<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVendors;
use App\Http\Requests\UpdateVendors;
use App\Http\Resources\Admin\VendorResource;
use App\Http\Resources\Admin\VendorsCollection;
use App\Models\Vendors;
use App\Repositories\Admin\VendorsRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendorsController extends Controller
{
    private $repository;

    /**
     * @param VendorsRepository $repository
     */
    public function __construct(VendorsRepository $repository)
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
        return new VendorsCollection($this->repository->getWithQuery(getDefaultPaginateCount($this->repository->getTableName())));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        return new VendorResource($this->repository->getOne($id, 'id'));
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
     * @param Request $request
     * @param int $vendorId
     * @return Response
     */
    public function actions(Request $request, int $vendorId)
    {
        $data = $request->validate([
            "action" => "required|string",
        ]);

        if (method_exists($this->repository, $data['action'])) {
            return [
                'status' => $this->repository->{$data['action']}($vendorId)
            ];
        }

        throw new Exception("Action '" . $data['action'] . "' not exist");
    }

    /**
     * @param int $pageType
     * @return Response
     */
    public function getOptions(string $pageType)
    {
        if ($pageType == 'edit') {
            $typesOptions = Vendors::$typeOptions;

            return [
                'typesOptions' => FunctionHelper::arrayToVueOptions($typesOptions)
            ];
        }

        return [];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateVendors $request, int $id)
    {
        $vendor = $this->repository->getOne($id);

        return new VendorResource($this->repository->update($vendor, $request->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateVendors $request)
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
