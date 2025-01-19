<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GridDataHelper;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UsersCollection extends ResourceCollection
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
                'bulkActions' => null,
                'options' => [],
                'filters' => $this->getFilters()
            ]
        ];
    }

    /**
     * @return App\Http\Resources\Admin\UserResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new UserResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'name' => __('users.table.name'),
            'email' => __('users.table.email'),
            'actions' => ''
        ];
    }

    /**
     * @return array
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new UserResource($collection))->toArray(Container::getInstance()->make('request'));
            return [
                'name' => [
                    'value' => $collection->getFullName()
                ],
                'email' => [
                    'value' => $cResource['email']
                ],
                'actions' => false,
                'index' => $cResource['id']
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

        return array_merge($defaultFilters);
    }
}
