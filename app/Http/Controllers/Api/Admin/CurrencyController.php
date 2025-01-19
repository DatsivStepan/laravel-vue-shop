<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Currency\CurrencyCollection;
use App\Http\Resources\Admin\Currency\CurrencyResource;
use App\Models\Currency;
use App\Repositories\Admin\CurrencyRepository;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CurrencyController extends Controller
{
    /**
     * @var CurrencyRepository
     */
    private $repository;
    /**
     * @var CurrencyService
     */
    private $service;

    public function __construct(CurrencyRepository $repository, CurrencyService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        $currencies = $this->repository->getCurrencyWithPagination(getDefaultPaginateCount());

        $resource = new CurrencyCollection($currencies);

        return $resource->response();
    }

    /**
     * @return JsonResponse
     */
    public function getCurrencyForSelect()
    {
        $currencies = $this->repository->getCurrencyForSelect();

        return response()->json($currencies);
    }

    public function show(Currency $currency)
    {
        return response()->json($currency);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
            'format' => 'string',
            'exchange_rate' => 'numeric|min:0',
            'active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $params = Arr::except($data, ['code', 'is_default']);

        $currency = $this->service->add($data['code'], $params, request('is_default', false));

        $resource = new CurrencyResource($currency);

        return $resource->response();
    }

    public function update(Request $request, Currency $currency)
    {
        $data = $request->validate([
            'format' => 'string',
            'exchange_rate' => 'numeric|min:0',
            'active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $params = Arr::except($data, ['is_default']);

        $currency = $this->service->update($currency, $params, request('is_default', false));

        $resource = new CurrencyResource($currency);

        return $resource->response();
    }
}
