<?php

namespace App\Http\Resources\Admin\Discounts;

use App\Helpers\DateHelper;
use App\Helpers\GridDataHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DiscountAssignProductCollection extends ResourceCollection
{
    /** @var int $discountID */
    private $discountID;

    public function __construct($resource, int $discountID)
    {
        parent::__construct($resource);
        $this->discountID = $discountID;
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
            'data' => $this->collection->map(function ($collection) {
                return $collection;
            }),
            'gridData' => [
                'headerItems' => [
                    __('products.product'),
                    __('products.assigned'),
                    ''
                ],
                'rowsItems' => $this->collection->map(function ($collection) {
                    return [
                        'id' => ['value' => $collection['id'], 'hidden' => true],
                        'name' => ['value' => $collection['name']],
                        'index' => ['value' => $collection['pivot']['product_id']],
                        'assigned' => [
                            'type' => 'datetime',
                            'value' => DateHelper::dateFormat($collection['created_at'])
                        ],
                        'actions' => [
                            GridDataHelper::generateDeleteButton(
                                route('api.discounts.detachProduct') . "?discount_id=$this->discountID&product_id={$collection['id']}",
                                __('system.delete')
                            )
                        ]
                    ];
                }),
                'options' => [],
                'bulkActions' => $this->getBulkActions(),
                'filters' => $this->getFilters(),
            ]
        ];
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => null,
            'details' => [
                'delete' => [
                    'url' => route('api.discounts.detachProduct'),
                    'method' => 'delete',
                    'params' => ['discount_id' => $this->discountID],
                    'ids' => 'product_id'
                ],
            ],
            'actions' => [
                'delete' => __('system.delete'),
            ],
        ];
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
