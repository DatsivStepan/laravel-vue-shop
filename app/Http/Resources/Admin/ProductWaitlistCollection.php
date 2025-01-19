<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GridDataHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductWaitlistCollection extends ResourceCollection
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
                'bulkActions' => $this->getBulkActions(),
                'filters' => $this->getFilters(),
                'options' => [],
            ]
        ];
    }

    /**
     * @return App\Http\Resources\Admin\VendorAssignResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new ProductWaitlistResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'customer' => [
                'value' => __('waitlist.table.customer'),
                'sort' => true,
            ],
            'product' => [
                'value' => __('waitlist.table.product'),
                'sort' => true,
            ],
            'created_at' => [
                'value' => __('waitlist.table.created'),
                'sort' => true,
            ],
            'seat_number' => [
                'value' => __('waitlist.table.seat_number'),
                'sort' => false,
            ],
            'status' => [
                'value' => __('waitlist.table.status'),
                'sort' => true,
            ],
            'actions' => '',
        ];
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/product-waitlist/bulk-actions',
            'actions' => [
                'delete' => __('system.delete')
            ]
        ];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $defaultFilters = GridDataHelper::getDefaultFilters();

        unset($defaultFilters['visible']);

        $addFilters = [];
        return array_merge($defaultFilters, $addFilters);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            return [
                'customer' => [
                    'value' => $collection->getUserName(),
                ],
                'product' => [
                    'value' => $collection->getProductName(),
                ],
                'created' => [
                    'type' => 'datetime',
                    'value' => $collection->created_at,
                ],
                'seat_number' => [
                    'value' => $collection->getSeatNumber(),
                ],
                'status' => [
                    'value' => $collection->getSentStatus(),
                ],
                'actions' => $this->getActions($collection),
                'index' => $collection->id,
            ];
        });
    }

    /**
     * @param App\Models\ProductWaitlist $record
     * @return array
     */
    public function getActions($collection)
    {
        return [
            GridDataHelper::generateActionApi(
                __('waitlist.send_email'),
                route('api.admin.waitlist.sendEmail', $collection->id)
            ),
            GridDataHelper::generateDeleteButton(
                '/admin/product-waitlist/' . $collection->id,
                __('system.delete')
            ),
        ];
    }
}
