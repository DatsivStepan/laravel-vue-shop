<?php

namespace App\Http\Controllers\Api\Admin;

use App\Services\Admin\Orders\RefundService;
use Illuminate\Http\JsonResponse;

class RefundController
{
    /**
     * @var RefundService
     */
    private $service;

    public function __construct(RefundService $service)
    {

        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function getRefundReasons(): JsonResponse
    {
        return response()->json($this->service->getRefundReasons());
    }
}
