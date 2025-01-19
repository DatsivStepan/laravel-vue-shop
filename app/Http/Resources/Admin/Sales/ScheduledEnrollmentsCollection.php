<?php

namespace App\Http\Resources\Admin\Sales;

use App\Helpers\GridDataHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Container\Container;

class ScheduledEnrollmentsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->getData(),
            'gridData' => [
                'headerItems' => $this->getHeaderItems(),
                'rowsItems' => $this->getRowsItems(),
                'filters' => $this->getFilters(),
                'bulkActions' => $this->getBulkActions(),
                'options' => []
            ]
        ];
    }

    /**
     * @return App\Http\Resources\Admin\ShippingResource
     */
    public function getData()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'user_name' => [
                'value' => __('orders.scheduled_enrollment.table.user_name'),
                'sort' => true
            ],
            'product_name' => [
                'value' => __('orders.scheduled_enrollment.table.product_name'),
                'sort' => true
            ],
            'purchased' => [
                'value' => __('orders.scheduled_enrollment.table.purchased'),
                'sort' => true
            ],
            'enrollment_time' => [
                'value' => __('orders.scheduled_enrollment.table.enrollment_time'),
                'sort' => true
            ],
            'order_id' => [
                'value' => __('orders.scheduled_enrollment.table.order_id'),
                'sort' => true
            ],
            'status' => [
                'value' => __('orders.scheduled_enrollment.table.status'),
                'sort' => true
            ],
            'actions' => ''
        ];
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/shippings/bulk-actions',
            'actions' => [
                'deleteIn' => __('system.delete')
            ]
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new ScheduledEnrollmentResource($collection))->toArray(Container::getInstance()->make('request'));
            $order = $collection->order;

            return [
                'user_name' => [
                    'value' => $order && $order->user ? $order->user->name : ''
                ],
                'product_name' => [
                    'value' => $cResource['name']
                ],
                'purchased' => [
                    'type' => 'datetime',
                    'value' => $cResource['created_at']
                ],
                'enrollment_time' => [
                    'type' => 'datetime',
                    'value' => $collection->product->enrol_start_date ?
                        Carbon::createFromFormat('Y-m-d H:i:s', $collection->product->enrol_start_date)->format(__('langconfig.iso8601')) : null
                ],
                'order_id' => [
                    'value' => $cResource['order_id']
                ],
                'status' => [
                    'value' => $collection->enrollStatus()
                ],
                'actions' => $this->getActions($collection),
                'index' => $collection->id
            ];
        });
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $filters = GridDataHelper::getDefaultFilters();
        unset($filters['visible']);

        $filters['paid_on_between'] = GridDataHelper::generateFilter('date_range', [
            'className' => '',
            'placeholder' => __('orders.filters.paid_on'),
            'options' => []
        ]);

        return $filters;
    }

    public function getActions($collection)
    {
        $actions = [];

        $actions[] = GridDataHelper::generateActionApi(
            __('system.enroll'),
            '/admin/scheduled-enrollment/enroll/' . $collection->id,
            []
        );

        $actions[] = GridDataHelper::generateDeleteButton(
            '/admin/scheduled-enrollment/' . $collection->id,
            __('system.delete')
        );

        return $actions;
    }
}
