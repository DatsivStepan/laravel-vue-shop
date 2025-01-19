<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GridDataHelper;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VendorsAssignCollection extends ResourceCollection
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
                'options' => [],
                'filters' => $this->getFilters()
            ]
        ];
    }

    /**
     * @return App\Http\Resources\Admin\VendorAssignResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new VendorAssignResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'name' => __('vendors.assign_table.name'),
            'assigned' => __('vendors.assign_table.assigned'),
            'actions' => ''
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
    public function getBulkActions()
    {
        return [
            'url' => '/admin/vendors-assign/bulk-actions',
            'actions' => [
                'delete' => __('system.delete')
            ]
        ];
    }

    /**
     * @return array
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new VendorAssignResource($collection))->toArray(Container::getInstance()->make('request'));
            return [
                'name' => [
                    'value' => $cResource['vendorName']
                ],
                'assigned' => [
                    'type' => 'datetime',
                    'value' => $cResource['created_at']
                ],
                'actions' => $this->getActions($cResource),
                'index' => $cResource['id']
            ];
        });
    }

    /**
     * @param App\Http\Resources\Admin\VendorAssignResource $record
     * @return array
     */
    public function getActions($record)
    {
        return [
            GridDataHelper::generateDeleteButton(
                '/admin/vendors-assign/' . $record['id'],
                __('system.delete')
            )
        ];
    }
}
