<?php

namespace App\Http\Controllers\Api\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SalesCollection;
use App\Notifications\OrderCompetedNotification;
use App\Repositories\Admin\SalesRepository;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class SalesController extends Controller
{
    /** @var OrdersRepository $repository */
    private $repository;

    /** @var OrderService $service */
    private $service;

    /**
     * @param OrdersRepository $repository
     * @param OrderService $service
     */
    public function __construct(SalesRepository $repository, OrderService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
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

        return new SalesCollection($this->repository->getWithPagination(getDefaultPaginateCount()));
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function manualApproval(int $id): JsonResponse
    {
        $order = $this->repository->getOneOrFail($id);

        return response()->json($this->service->approval($order));
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function manualReject(int $id): JsonResponse
    {
        $order = $this->repository->getOneOrFail($id);

        return response()->json($this->service->reject($order));
    }


    /**
     * @param Request $request
     * @param int $orderId
     * @return array
     */
    public function printInvoice(Request $request, int $orderId)
    {
        $order = $this->repository->getOneOrFail($orderId, 'id');
        $url = $this->service->generateInvoice($order);

        return [
            'status' => true,
            'path' => route('get_pdf',
                [
                    'id' => getSetting('connection_id'),
                    'url' => $this->service->generateInvoice($order),
                ]
            ),
            'name' => explode('/', base64_decode($url))[1],
        ];
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
     * @param int $orderId
     * @return Response
     */
    public function repeatEmail(Request $request, int $orderId)
    {
        $order = $this->repository->getOneOrFail($orderId, 'id');
        return [
            'status' => $order->user->notify(new OrderCompetedNotification($order))
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
}
