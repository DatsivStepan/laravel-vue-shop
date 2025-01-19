<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\RolesRepository;
use App\Services\Admin\Orders\RefundService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $repository;
    /**
     * @var RefundService
     */
    private $refundService;

    /**
     * @param RolesRepository $repository
     * @param RefundService $refundService
     */
    public function __construct(RolesRepository $repository, RefundService $refundService)
    {
        $this->repository = $repository;

        $this->refundService = $refundService;
    }

    /**
     * @param Request $request
     * @throws \App\Exceptions\InvalidOrderRefundPaymentStatus
     * @throws \Exception
     */
    public function refund(Request $request)
    {
        $payload = $request->validate([
            'order_id' => 'required|integer',
            'refund_amount' => 'required|numeric',
            'is_manual' => 'boolean',
            'reason' => 'string|nullable',
        ]);

        $res = $this->refundService->refund($payload);

        if (!$res) {
            throw new \Exception(__('errors.refund.fail_refund'));
        }

    }

}
