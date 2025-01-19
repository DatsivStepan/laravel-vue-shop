<?php

namespace App\Http\Resources\Admin\Sales;

use App\Helpers\GridDataHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Container\Container;

class SeatsCollection extends ResourceCollection
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
                'value' => __('orders.seats.table.user_name'),
                'sort' => true
            ],
            'product_name' => [
                'value' => __('orders.seats.table.product_name'),
                'sort' => true
            ],
            'key' => [
                'value' => __('orders.seats.table.key'),
                'sort' => true
            ],
            'seats_count' => [
                'value' => __('orders.seats.table.seats_count'),
                'sort' => true
            ],
            'seats_used' => [
                'value' => __('orders.seats.table.seats_used'),
                'sort' => true
            ],
            'created' => [
                'value' => __('orders.seats.table.created'),
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
            $resource = (new SeatResource($collection))->toArray(Container::getInstance()->make('request'));
            $orderProduct = $collection->orderProduct;
            $order = $orderProduct->order;

            return [
                'user_name' => [
                    'value' => $order->user->name
                ],
                'product_name' => [
                    'value' => $orderProduct->name
                ],
                'key' => [
                    'value' => $resource['seat_key']
                ],
                'seats_count' => [
                    'value' => $orderProduct->quantity
                ],
                'seats_used' => [
                    'value' => $resource['used_count']
                ],
                'created' => [
                    'value' => Carbon::createFromFormat('Y-m-d H:i:s', $resource['created_at'])->format(__('langconfig.strfdatetime'))
                ],
                'actions' => $this->getActions($resource),
                'index' => $resource['id'],
                'activeRow' => $resource['status']
            ];
        });
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $filters = GridDataHelper::getDefaultFilters();
        $status = $filters['visible'];
        unset($filters['visible']);
        $filters['status'] = $status;

        $filters['paid_on_between'] = GridDataHelper::generateFilter('date_range', [
            'className' => '',
            'placeholder' => __('orders.filters.paid_on'),
            'options' => []
        ]);

        return $filters;
    }

    public function getActions($resource)
    {
        $actions = [];

        $sAction = $resource['status'] ? 'hide' : 'show';
            $actions[] = GridDataHelper::generateActionApi(__('system.' . $sAction),
                '/admin/seats/actions/' . $resource['id'],
                [
                    'action' => $sAction,
                    'status' => $resource['status'],
                ]);

        $actions[] = GridDataHelper::generateDeleteButton(
            '/admin/seats/' . $resource['id'],
            __('system.delete')
        );

        $actions[] = GridDataHelper::generateActionLink(
            __('orders.seats.actions.details'),
            'admin-order-seats-details',
            ['id' => $resource['id']]
        );

        return $actions;
    }
}
