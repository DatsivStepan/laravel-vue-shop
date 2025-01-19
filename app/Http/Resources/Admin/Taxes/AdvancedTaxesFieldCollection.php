<?php

namespace App\Http\Resources\Admin\Taxes;

use App\Helpers\GridDataHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Taxes\AdvancedTaxesField */
class AdvancedTaxesFieldCollection extends ResourceCollection
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
            'data' => $this->collection,
            'gridData' => [
                'headerItems' => $this->getHeaderItems(),
                'rowsItems' => $this->getRowsItems(),
                'options' => [],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'name' => [
                'value' => __('taxes.fields.name'),
                'sort' => true
            ],
            'category' => __('taxes.fields.category'),
            'created_at' => [
                'value' => __('system.created'),
                'sort' => true
            ],
            'actions' => '',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new AdvancedTaxesFieldResource($collection))->toArray(request());

            return [
                'id' => [
                    'value' => $cResource['id'],
                    'hidden' => true,
                ],
                'key' => [
                    'value' => $cResource['key'],
                    'hidden' => true,
                ],
                'name' => [
                    'value' => $cResource['name'],
                ],
                'category' => [
                    'value' => __('taxes.profile_fields'),
                ],
                'created' => [
                    'type' => 'datetime',
                    'value' => $collection['created_at'],
                ],

                'actions' => $this->getActions($cResource),
            ];
        });
    }


    /**
     * @param array $record
     * @return array
     */
    public function getActions(array $record)
    {
        $actions = [];
        $actions[] = GridDataHelper::generateEditButton(
            'admin-advance-taxes-values',
            $record['id'],
            __('taxes.field_values')
        );

        $actions[] = GridDataHelper::generateDeleteButton(
            route('api.taxes.fields.destroy') . "?ids=${record['id']}",
            __('system.delete')
        );

        return $actions;
    }

}
