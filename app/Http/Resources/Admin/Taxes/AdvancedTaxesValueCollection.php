<?php

namespace App\Http\Resources\Admin\Taxes;

use App\Helpers\GridDataHelper;
use App\Models\Taxes\AdvancedTaxesField;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Taxes\AdvancedTaxesValue */
class AdvancedTaxesValueCollection extends ResourceCollection
{
    private $field;

    public function __construct($resource, AdvancedTaxesField $field)
    {
        parent::__construct($resource);
        $this->field = $field;
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
            'field_name' => $this->field->name,
            'data' => $this->collection,
            'gridData' => [
                'headerItems' => $this->getHeaderItems(),
                'rowsItems' => $this->getRowsItems(),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        return [
            'value' => __('taxes.values.values'),
            'tax' => __('taxes.values.tax'),
            'created' => __('system.created'),
            'actions' => '',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new AdvancedTaxesValueResource($collection))->toArray(request());

            return [
                'id' => [
                    'value' => $cResource['id'],
                    'hidden' => true,
                ],
                'value' => [
                    'value' => $cResource['value'],
                ],
                'tax' => [
                    'value' => $cResource['tax'],
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
        $actions[] = GridDataHelper::generateDeleteButton(
            route('api.taxes.values.destroy') . "?ids=${record['id']}",
            __('system.delete')
        );

        return $actions;
    }
}
