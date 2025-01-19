<?php

namespace App\Http\Resources\Admin\Sales;

use App\Helpers\GridDataHelper;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShippingsCollection extends ResourceCollection
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
        return $this->collection->map(function ($collection) {
            return new ShippingResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'order_id' => [
                'value' => __('shipping.table.order_id'),
                'sort' => true
            ],
            'products' => __('shipping.table.products'),
            'name' => [
                'value' => __('shipping.table.name'),
                'sort' => true
            ],
            'email' => [
                'value' => __('shipping.table.email'),
                'sort' => true
            ],
            'city' => [
                'value' => __('shipping.table.city'),
                'sort' => true
            ],
            'state' => [
                'value' => __('shipping.table.state'),
                'sort' => true
            ],
            'zip_code' => [
                'value' => __('shipping.table.zip_code'),
                'sort' => true
            ],
            'phone' => [
                'value' => __('shipping.table.phone'),
                'sort' => true
            ],
            'note' => [
                'value' => __('shipping.table.notes'),
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
            $cResource = (new ShippingResource($collection))->toArray(Container::getInstance()->make('request'));
            $order = $collection->order;

            return [
                'order_id' => [
                    'value' => $cResource['order_id']
                ],
                'products' => [
                    'value' => $order ? implode(', ', $collection->order->products()->pluck('products.name')->all()) : ''
                ],
                'name' => [
                    'value' => $cResource['name']
                ],
                'email' => [
                    'value' => $cResource['email']
                ],
                'city' => [
                    'value' => $cResource['city']
                ],
                'state' => [
                    'value' => $cResource['state']
                ],
                'zip_code' => [
                    'value' => $cResource['zip_code']
                ],
                'phone' => [
                    'value' => $cResource['phone']
                ],
                'note' => [
                    'value' => $cResource['note']
                ],
                'actions' => false,
                'index' => $collection->id
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
