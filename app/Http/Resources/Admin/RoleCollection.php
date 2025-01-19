<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GridDataHelper;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
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
                'options' => []
            ]
        ];
    }

    /**
     * @return App\Http\Resources\Admin\CategoryResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new RoleResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'id' => __('roles.table.id'),
            'name' => __('roles.table.name'),
            'actions' => ''
        ];
    }

    /**
     * @return array
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new RoleResource($collection))->toArray(Container::getInstance()->make('request'));

            return [
                'id' => [
                    'value' => $cResource['id']
                ],
                'name' => [
                    'value' => $cResource['name']
                ],
                'actions' => $this->getActions($cResource)
            ];
        });
    }

    /**
     * @param App\Http\Resources\Admin\CategoryResource $record
     * @return array
     */
    public function getActions($record)
    {
        $actions = [];
        $actions[] = GridDataHelper::generateActionLink(
            __('roles.lms_assign.assign_lms_roles'),
            'admin-assigns-lms-roles',
            ['id' => $record['id']]
        );

        $actions[] = GridDataHelper::generateShowButton(
            'admin-roles-show',
            $record['id'],
            __('roles.permissions')
        );

        $actions[] = GridDataHelper::generateEditButton(
            'admin-roles-edit',
            $record['id'],
            __('system.edit')
        );

        return $actions;
    }
}
