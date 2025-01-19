<?php

namespace App\Http\Resources\Admin\Sales;

use App\Helpers\GridDataHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SeatsDetailsCollection extends ResourceCollection
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
                'value' => __('orders.seats_details.table.user_name'),
                'sort' => true
            ],
            'product_name' => [
                'value' => __('orders.seats_details.table.product_name'),
                'sort' => true
            ],
            'created' => [
                'value' => __('orders.seats_details.table.created'),
                'sort' => true
            ],
            'status' => __('orders.seats_details.table.status'),
            'actions' => ''
        ];
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/seats/bulk-actions',
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
            $order = $collection->order;
            $orderProduct = $collection->ordersProductSeats->orderProduct;

            return [
                'user_name' => [
                    'value' => $order && $order->user ? $order->user->name : ''
                ],
                'product_name' => [
                    'value' => $orderProduct->name
                ],
                'created' => [
                    'value' => Carbon::createFromFormat('Y-m-d H:i:s', $collection->created_at)->format(__('langconfig.strfdatetime'))
                ],
                'status' => [
                    'value' => $order->getStatus()
                ],
                'index' => $orderProduct->id,
            ];
        });
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $defaultFilters = GridDataHelper::getDefaultFilters();
        unset($defaultFilters['visible']);

        return $defaultFilters;
    }
}
