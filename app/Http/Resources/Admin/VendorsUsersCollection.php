<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GridDataHelper;
use App\Models\VendorsUsers;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class VendorsUsersCollection extends ResourceCollection
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
     * @return App\Http\Resources\Admin\VendorUsersResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new VendorUsersResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'name' => __('vendors.assign_user_table.name'),
            'type' => __('vendors.assign_user_table.type'),
            'assigned' => __('vendors.assign_user_table.assigned'),
            'actions' => ''
        ];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $filters = GridDataHelper::getDefaultFilters();

        unset($filters['visible']);

        $options = Arr::prepend(
            VendorsUsers::getTypes(),
            __('system.show_all'),
            ''
        );
        $filters['user_type'] = GridDataHelper::generateFilter('select', [
            'className' => 'w-25',
            'placeholder' =>__('vendors.form_users.select_types'),
            'options' => $options,
            'value' => ''
        ]);

        return $filters;
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/vendors-users/bulk-actions',
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
            $cResource = (new VendorUsersResource($collection))->toArray(Container::getInstance()->make('request'));
            $user = $collection->user;
            return [
                'name' => [
                    'value' => $user ? $user->getFullName() : null
                ],
                'type' => [
                    'value' => $collection->getUserType()
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
     * @param App\Http\Resources\Admin\VendorUsersResource $record
     * @return array
     */
    public function getActions($record)
    {
        return [
            GridDataHelper::generateDeleteButton(
                '/admin/vendors-users/destroy/' . $record['id'],
                __('system.delete')
            )
        ];
    }
}
