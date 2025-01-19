<?php

namespace App\Http\Resources\Admin\Currency;

use App\Helpers\GridDataHelper;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CurrencyCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $data = $this->getData()->toArray();
        return [
            'data' => $data,
            'gridData' => [
                'headerItems' => $this->getHeaderItems(),
                'rowsItems' => $this->getRowsItems($data),
                'filters' => $this->getFilters(),
                'options' => [],
            ],
        ];
    }

    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return (new CurrencyResource($collection))->toArray(request());
        });
    }


    public function getHeaderItems()
    {
        return [
            'name' => [
                'value' => __('currency.name'),
                'sort' => true
            ],
            'is_default' => __('currency.is_default'),
            'code' => [
                'value' => __('currency.code'),
                'sort' => true
            ],
            'symbol' => [
                'value' => __('currency.symbol'),
                'sort' => true
            ],
            'rate' => __('currency.rate'),
            'created' => [
                'value' => __('system.created_at'),
                'sort' => true
            ],
            'actions' => '',
        ];
    }

    public function getRowsItems(array $data)
    {
        return collect($data)->map(function ($collection) {
            return [
                'name' => [
                    'value' => $collection['name'],
                ],
                'is_default' => [
                    'value' => $collection['is_default'] ? __('system.yes') : __('system.no'),
                ],
                'code' => [
                    'value' => $collection['code'],
                ],
                'symbol' => [
                    'value' => $collection['symbol'],
                ],
                'rate' => [
                    'value' => $collection['exchange_rate'],
                ],
                'created_at' => [
                    'type' => 'datetime',
                    'value' => $collection['created_at'],
                ],
                'actions' => $this->getActions($collection),
            ];
        });
    }

    public function getActions($record)
    {
        $actions = [];

        $actions[] = GridDataHelper::generateEditButton(
            'admin-currencies-edit',
            $record['id'],
            __('system.edit')
        );


        $title = $record['active'] ? __('system.hide') : __('system.show');
        $actions[] = GridDataHelper::generateActionApi(
            $title,
            route('api.currencies.update', $record['id']),
            ['active' => !$record['active']],
            'patch'
        );

        return $actions;
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
