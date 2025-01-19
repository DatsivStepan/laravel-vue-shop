<?php

namespace App\Http\Resources\Admin\Discounts;

use App\Facades\UserSettings;
use App\Helpers\GridDataHelper;
use App\Repositories\Admin\DiscountRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class DiscountCollection extends ResourceCollection
{
    /** @var DiscountRepository $repository */
    private $repository;
    private $user;

    public function __construct($resource, $user)
    {
        parent::__construct($resource);

        $this->repository = app(DiscountRepository::class);
        $this->user = $user;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->getData();
        return [
            'data' => $data,
            'gridData' => [
                'headerItems' => $this->getHeaderItems(),
                'rowsItems' => $this->getRowsItems($data),
                'filters' => $this->getFilters(),
                'options' => [],
                'bulkActions' => $this->getBulkActions(),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => route('api.discounts.bulkActions', ''),

            'actions' => [
                'active' => __('system.active'),
                'inactive' => __('system.inactive'),
                'delete' => __('system.delete'),
            ],
        ];
    }

    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new DiscountResource($collection);
        });
    }

    public function getHeaderItems()
    {
        return [
            'name' => [
                'value' => __('discounts.name'),
                'sort' => true
            ],
            'time_start' => [
                'value' => __('system.starttime'),
                'sort' => true
            ],
            'time_end' => [
                'value' => __('system.endtime'),
                'sort' => true
            ],
            'discount' => [
                'value' => __('discounts.discount'),
                'sort' => true
            ],
            'max_applied_products' => [
                'value' => __('discounts.applied'),
                'sort' => true
            ],
            'created_at' => [
                'value' => __('system.created'),
                'sort' => true
            ],
            'actions' => '',
        ];
    }

    public function getRowsItems(Collection $collection)
    {
        return $collection->map(function ($collection, $index) {

            return [
                'name' => [
                    'value' => $collection['name'],
                ],
                'index' => $collection['id'],
                'timestart' => [
                    'value' => $collection['time_start'],
                ],
                'timeend' => [
                    'value' => $collection['time_end'],
                ],
                'discount' => [
                    'value' => "${collection['discount']}%",
                ],
                'applied' => [
                    'value' => $this->repository->applied($collection['id']),
                ],
                'created_at' => [
                    'type' => 'datetime',
                    'value' => $collection['created_at'],
                ],
                'actions' => $this->getActions($collection, $index),
                'activeRow' => $collection['status']
            ];
        });
    }

    public function getActions($record, &$k)
    {
        $actions = [];

        $actions[] = GridDataHelper::generateEditButton(
            'admin-discounts-edit',
            $record['id'],
            __('system.edit')
        );

        $title = $record['status'] ? __('system.hide') : __('system.show');
        $actions[] = GridDataHelper::generateActionApi(
            $title,
            route('api.discounts.update', $record['id']),
            ['status' => !$record['status']],
            'put'
        );

        $actions[] = GridDataHelper::generateActionLink(
            __('products.assign_products'),
            'admin-discounts-products-assigns',
            ['id' => $record['id']]
        );

        if (getSetting('enable_vendors')) {
            $actions[] = GridDataHelper::generateActionLink(
                __('vendors.assignvendors'),
                'admin-discounts-vendors-assigns',
                ['id' => $record['id']]
            );
        }

        $actions[] = GridDataHelper::generateDeleteButton(
            route('api.discounts.destroy', $record['id']),
            __('system.delete')
        );

        return $actions;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $defaultFilters = GridDataHelper::getDefaultFilters();
        $status = $defaultFilters['visible'];
        unset($defaultFilters['visible']);

        $addFilters = ['status' => $status];
        $addFilters['status']['options']['value'] = UserSettings::get($this->user, 'discounts_filter_status');
        return array_merge($defaultFilters, $addFilters);
    }
}
