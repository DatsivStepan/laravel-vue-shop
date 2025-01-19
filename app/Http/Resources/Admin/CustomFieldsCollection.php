<?php

namespace App\Http\Resources\Admin;

use App\Helpers\GridDataHelper;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomFieldsCollection extends ResourceCollection
{
    private $user;
    private $itemsCount = 0;
    private $requestData = [];

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
        $this->requestData = $request->all();
        if (array_key_exists('page', $this->requestData)) {
            unset($this->requestData['page']);
        }

        $this->itemsCount = count($this->collection);
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
     * @return App\Http\Resources\Admin\CustomFieldResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new CustomFieldResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'title' => __('custom_fields.table.title'),
            'fieldtype' => __('custom_fields.table.field_type'),
            'actions' => ''
        ];
    }

    /**
     * @return array
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new CustomFieldResource($collection))->toArray(Container::getInstance()->make('request'));
            return [
                'title' => [
                    'value' => $cResource['title'],
                ],
                'fieldtype' => [
                    'value' => $collection->getFieldType(),
                ],
                'activeRow' => $collection['visible'],
                'actions' => $this->getActions($cResource),
                'index' => $cResource['id'],
            ];
        });
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/custom-fields/bulk-actions',
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

        $addFilters = [];
        return array_merge($defaultFilters, $addFilters);
    }

    /**
     * @param App\Http\Resources\Admin\CustomFieldResource $record
     * @return array
     */
    public function getActions($record)
    {
        $actions = [];

        $actions[] = GridDataHelper::generateEditButton(
            'admin-custom-fields-edit',
            $record['id'],
            __('system.edit')
        );

        $sAction = $record['visible'] ? 'hide' : 'show';
        $actions[] = GridDataHelper::generateActionApi(__('system.' . $sAction),
            '/admin/custom-fields/actions/' . $record['id'],
            [
                'action' => $sAction,
                'status' => $record['visible'],
            ]);

        $actions[] = GridDataHelper::generateDeleteButton(
            '/admin/custom-fields/' . $record['id'],
            __('system.delete')
        );

        return $actions;
    }
}
