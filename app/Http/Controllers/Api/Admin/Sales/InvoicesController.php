<?php

namespace App\Http\Controllers\Api\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\InvoicesCollection;
use App\Repositories\Admin\SalesRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    private $repository;

    /**
     * @param SalesRepository $repository
     */
    public function __construct(SalesRepository $repository)
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

        return new InvoicesCollection($this->repository->getWithPagination(getDefaultPaginateCount()));
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
}
