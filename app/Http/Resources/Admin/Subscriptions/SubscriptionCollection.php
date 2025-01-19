<?php

namespace App\Http\Resources\Admin\Subscriptions;

use App\Helpers\GridDataHelper;
use App\Repositories\Admin\DiscountRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class SubscriptionCollection extends ResourceCollection
{
    /** @var DiscountRepository $repository */
    private $repository;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->repository = app(DiscountRepository::class);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->getData();
        return [
            'data' => [
                'headerItems' => $this->getHeaderItems(),
                'rowsItems' => $this->getRowsItems($data),
                'filters' => $this->getFilters(),
                'options' => [],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [];
    }

    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new SubscriptionResource($collection);
        });
    }

    public function getHeaderItems()
    {
        return [
            'customer' => __('subscriptions.customer'),
            'product' => __('subscriptions.product'),
            'subscription_date' => __('subscriptions.date'),
            'price' => __('subscriptions.price'),
            'recurring_period' => __('subscriptions.recurring_period'),
            'billing_cycles' => __('subscriptions.billing_cycles'),
            'status' => __('subscriptions.status'),
            'actions' => '',
        ];
    }

    public function getRowsItems(Collection $collection)
    {
        return $collection->map(function ($subscription) {
            $subscription = (new SubscriptionResource($subscription))->toArray(request());

            return [
                'customer' => [
                    'value' => $subscription['customer'],
                ],
                'product' => [
                    'value' => $subscription['product'],
                ],
                'subscription_date' => [
                    'type' => 'datetime',
                    'value' => $subscription['subscription_date'],
                ],
                'price' => [
                    'value' => $subscription['price'],
                ],
                'recurring_period' => [
                    'value' => $subscription['recurring_period'],
                ],
                'billing_cycles' => [
                    'value' => $subscription['billing_cycles'],
                ],
                'status' => [
                    'value' => $subscription['status'],
                ],

                'actions' => $this->getActions($subscription),
            ];
        });
    }

    public function getActions($record)
    {
        $actions = [];

        $actions[] = GridDataHelper::generateActionApi(
            __('subscriptions.expire'),
            route('api.subscriptions.expire', $record['id']),
            [],
            'patch'
        );
        $actions[] = GridDataHelper::generateActionApi(
            __('subscriptions.cancel'),
            route('api.subscriptions.cancel', $record['id']),
            [],
            'patch'
        );

        return $actions;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [];
    }
}
