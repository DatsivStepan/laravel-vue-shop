<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GridDataHelper;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class VendorsCollection extends ResourceCollection
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
     * @return App\Http\Resources\Admin\VendorResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new VendorResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'name' => [
                'value' => __('vendors.table.name'),
                'sort' => true
            ],
            'email' => [
                'value' => __('vendors.table.email'),
                'sort' => true
            ],
            'company' => [
                'value' => __('vendors.table.company'),
                'sort' => true
            ],
            'actions' => ''
        ];
    }

    /**
     * @return array
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new VendorResource($collection))->toArray(Container::getInstance()->make('request'));
            return [
                'name' => [
                    'value' => $cResource['name']
                ],
                'email' => [
                    'value' => $cResource['email']
                ],
                'company' => [
                    'value' => $cResource['company']
                ],
                'actions' => $this->getActions($cResource),
                'activeRow' => $cResource['status'],
                'index' => $cResource['id']
            ];
        });
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/vendors/bulk-actions',
            'actions' => [
                'hide' => __('system.hide'),
                'show' => __('system.show'),
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
        $status = $defaultFilters['visible'];
        unset($defaultFilters['visible']);

        $addFilters = [
            'status' => $status
        ];
        return array_merge($defaultFilters, $addFilters);
    }

    /**
     * @param App\Http\Resources\Admin\VendorResource $record
     * @return array
     */
    public function getActions($record)
    {
        $actions = [];
        $user = Auth::user();
        if ($user->can('canEditVendors')) {
            $actions[] = GridDataHelper::generateEditButton(
                'admin-vendors-edit',
                $record['id'],
                __('system.edit')
            );
        }

        if ($user->can('manageAssignUsers')) {
            $actions[] = GridDataHelper::generateActionLink(
                __('vendors.users_assign'),
                'admin-vendors-users',
                ['id' => $record['id']]
            );
        }

        if ($user->can('canEditVendors')) {
            $sAction = $record['status'] ? 'hide' : 'show';
            $actions[] = GridDataHelper::generateActionApi(__('system.' . $sAction),
                '/admin/vendors/actions/' . $record['id'],
                [
                    'action' => $sAction,
                    'status' => $record['status'],
                ]);
        }

        if ($user->can('canDeleteVendors')) {
            $actions[] = GridDataHelper::generateDeleteButton(
                '/admin/vendors/' . $record['id'],
                __('system.delete')
            );
        }

        return $actions;
    }
}
