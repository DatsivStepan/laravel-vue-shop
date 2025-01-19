<?php

namespace App\Http\Resources;

use App\Helpers\GridDataHelper;
use App\Http\Resources\ProductFavoriteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductFavoritesCollection extends ResourceCollection
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
     * @return App\Http\Resources\ProductFavoriteResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new ProductFavoriteResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'product' => [
                'value' => __('waitlist.table.product'),
                'sort' => true
            ],
            'created_at' => [
                'value' => __('waitlist.table.created'),
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
            'url' => '/products-favorites/bulk-actions',
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
     * @return array
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            return [
                'product' => [
                    'value' => $collection->getProductName()
                ],
                'created' => [
                    'type' => 'datetime',
                    'value' => $collection->created_at
                ],
                'actions' => $this->getActions($collection),
                'index' => $collection->id
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
            GridDataHelper::generateDeleteButton(
                '/products-favorites/' . $collection->id,
                __('system.delete')
            )
        ];
    }
}
