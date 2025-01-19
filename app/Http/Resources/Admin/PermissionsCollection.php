<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GridDataHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\Permission\Models\Role;

class PermissionsCollection extends ResourceCollection
{
    private $role;

    public function __construct($resource, Role $role)
    {
        parent::__construct($resource);

        $this->role = $role;
    }

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
                'options' => []
            ]
        ];
    }

    /**
     * @return App\Http\Resources\Admin\PermissionResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new PermissionResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'name' => __('roles.permission_table.name'),
            'enable' => __('roles.permission_table.enable'),
        ];
    }

    /**
     * @return array
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            return [
                'name' => [
                    'value' => __('permissions.' . $collection->name)
                ],
                'enable' => [
                    'type' => 'switch',
                    'value' => $this->role->hasPermissionTo($collection->name),
                    'options' => GridDataHelper::generateActionApi(
                        __('roles.enable_permission'),
                        '/admin/permissions/change-permission-status',
                        [
                            'permissionId' => $collection->id,
                            'roleId' => $this->role->id,
                            'value' => !$this->role->hasPermissionTo($collection->name)
                        ])
                ],
                'index' => $collection->id,
                'activeRow' => count($collection->roles) ? true : false,
                'actions' => false
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

        $addFilters = [];
        return array_merge($defaultFilters, $addFilters);
    }
}
