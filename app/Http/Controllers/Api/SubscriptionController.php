<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Admin\Subscriptions\SubscriptionCollection;
use App\Http\Resources\Admin\Subscriptions\SubscriptionResource;
use App\Http\Resources\ProductSubscribeResource;
use App\Models\Products;
use App\Models\Subscription;
use App\Repositories\Admin\SubscriptionRepository;
use App\Rules\SubscriptionBillingType;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SubscriptionController
{
    /**
     * @var SubscriptionService
     */
    private $service;
    /**
     * @var SubscriptionRepository
     */
    private $repository;

    public function __construct(SubscriptionService $service, SubscriptionRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return ProductSubscribeResource
     */
    public function getSubscriptionData(Request $request): ProductSubscribeResource
    {
        $request->validate([
            'product_id' => ['required', new SubscriptionBillingType()],
        ]);

        $product = Products::find($request->product_id);

        return (new ProductSubscribeResource($product));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function subscribe(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'product_id' => ['required', new SubscriptionBillingType()],
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'payment_type' => 'string',
            'payment_method_id' => 'integer|nullable',
            'extra_data' => 'nullable',
        ]);

        $result = $this->service->subscribe($payload, auth()->id());

        return response()->json($result, 201);
    }

    /**
     * @param Subscription $subscription
     */
    public function cancel(Subscription $subscription)
    {
        $subscription = $this->service->makeCanceled($subscription);

        return (new SubscriptionResource($subscription))->response();
    }

    /**
     * @param Subscription $subscription
     */
    public function expire(Subscription $subscription)
    {
        $subscription = $this->service->makeExpired($subscription);

        return (new SubscriptionResource($subscription))->response();
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $subscriptions = $this->repository->paginate(request('per_page', getDefaultPaginateCount()));

        return (new SubscriptionCollection($subscriptions))->response();
    }

    /**
     * @param Subscription $subscription
     * @return JsonResponse
     */
    public function show(Subscription $subscription): JsonResponse
    {
        return (new SubscriptionResource($subscription))->response();
    }

    /**
     * @return JsonResponse
     */
    public function mySubscriptions(): JsonResponse
    {
        $subscriptions = $this->repository->getUserSubscription(auth()->id(),
            request('per_page', getDefaultPaginateCount()));

        return (new SubscriptionCollection($subscriptions))->response();
    }
}
