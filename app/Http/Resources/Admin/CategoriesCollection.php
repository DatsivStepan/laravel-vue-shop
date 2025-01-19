<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GridDataHelper;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Facades\UserSettings;

class CategoriesCollection extends ResourceCollection
{
    private $user;

    public function __construct($resource, $user)
    {
        parent::__construct($resource);

        $this->user = $user;
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
                'bulkActions' => $this->getBulkActions(),
                'filters' => $this->getFilters(),
                'options' => [
                    'enableDraggble' => true
                ],
            ]
        ];
    }

    /**
     * @return App\Http\Resources\Admin\CategoryResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new CategoryResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'name' => [
                'value' => __('categories.name'),
                'sort' => true
            ],
            'id_number' => [
                'value' => __('categories.idnumber'),
                'sort' => true
            ],
            'products' => __('categories.productscount'),
            'actions' => ''
        ];
    }

    /**
     * @return array
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new CategoryResource($collection))->toArray(Container::getInstance()->make('request'));
            return [
                'name' => [
                    'value' => $cResource['name'],
                    'type' => 'link',
                    'params' => [
                        'routeName' => 'admin-categories',
                        'routeParams' => ['id' => $cResource['id']],
                    ]
                ],
                'category_id' => [
                    'value' => $cResource['id_number']
                ],
                'products' => [
                    'value' => $cResource['products_count']
                ],
                'actions' => $this->getActions($cResource),
                'index' => $cResource['id'], // not show in grid
                'activeRow' => $cResource['visible']
            ];
        });
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $defaultFilters = GridDataHelper::getDefaultFilters();
        $defaultFilters['visible']['options']['value'] = UserSettings::get($this->user, 'categories_filter_status');
        $addFilters = [];
        return array_merge($defaultFilters, $addFilters);
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/categories/bulk-actions',
            'actions' => [
                'hide' => __('system.hide'),
                'show' => __('system.show'),
                'deleteIn' => __('system.delete')
            ]
        ];
    }

    /**
     * @param App\Http\Resources\Admin\CategoryResource $record
     * @return array
     */
    public function getActions($record)
    {
        $actions = [];

        if ($this->user->can('canEditCategory')) {
            $actions[] = GridDataHelper::generateEditButton(
                'admin-categories-edit',
                $record['id'],
                __('system.edit')
            );

            $sAction = $record['visible'] ? 'hide' : 'show';
            $actions[] = GridDataHelper::generateActionApi(__('system.' . $sAction),
                '/admin/categories/actions/' . $record['id'],
                [
                    'action' => $sAction,
                    'status' => $record['visible'],
                ]);
        }

        if ($this->user->can('manageVendors')) {
            $actions[] = GridDataHelper::generateActionLink(
                __('categories.actions.assign_vendors'),
                'admin-categories-vendors-assigns',
                ['id' => $record['id']]
            );
        }

        if ($this->user->can('canDeleteCategory') && !$record['products_count']) {
            $actions[] = GridDataHelper::generateDeleteButton(
                '/admin/categories/' . $record['id'],
                __('system.delete')
            );
        }

        return $actions;
    }
}
