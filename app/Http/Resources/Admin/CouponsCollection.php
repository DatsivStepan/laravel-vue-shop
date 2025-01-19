<?php

namespace App\Http\Resources\Admin;

use App\Facades\UserSettings;
use App\Helpers\GridDataHelper;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CouponsCollection extends ResourceCollection
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
     *
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
                'bulkActions' => $this->getBulkActions(),
                'options' => [],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => route('api.coupons.bulkActions', ''),

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
            return new CouponResource($collection);
        });
    }

    public function getHeaderItems()
    {
        return [
            'code' => [
                'value' => __('coupons.code'),
                'sort' => true
            ],
            'discount' => [
                'value' => __('coupons.discount'),
                'sort' => true
            ],
            'timestart' => [
                'value' => __('system.starttime'),
                'sort' => true
            ],
            'timeend' => [
                'value' => __('system.endtime'),
                'sort' => true
            ],
            'used' => __('system.used'),
            'created_at' => [
                'value' => __('system.created'),
                'sort' => true
            ],
            'actions' => '',
        ];
    }

    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new CouponResource($collection))->toArray(Container::getInstance()->make('request'));

            return [
                'index' => $collection['id'],
                'code' => [
                    'value' => $cResource['code'],
                ],
                'discount' => [
                    'value' => $cResource['formatted_discount'],
                ],
                'timestart' => [
                    'type' => 'datetime',
                    'value' => $cResource['timestart'],
                ],
                'timeend' => [
                    'type' => 'datetime',
                    'value' => $cResource['timeend'],
                ],
                'used' => [
                    'value' => 0,
                ],
                'created_at' => [
                    'type' => 'datetime',
                    'value' => $cResource['created_at'],
                ],
                'actions' => $this->getActions($cResource),
                'activeRow' => $cResource['status']
            ];
        });
    }

    public function getActions($record)
    {
        $actions = [];

        $actions[] = GridDataHelper::generateEditButton(
            'admin-coupons-edit',
            $record['id'],
            __('system.edit')
        );

        $title = $record['status'] ? __('system.hide') : __('system.show');
        $actions[] = GridDataHelper::generateActionApi(
            $title,
            route('api.coupons.update', $record['id']),
            ['status' => !$record['status']],
            'put'
        );

        $actions[] = GridDataHelper::generateActionLink(
            __('coupons.actions.assign_products'),
            'admin-coupons-products-assigns',
            ['id' => $record['id']]
        );

        if (getSetting('enable_vendors')) {
            $actions[] = GridDataHelper::generateActionLink(
                __('coupons.actions.assign_vendors'),
                'admin-coupons-vendors-assigns',
                ['id' => $record['id']]
            );
        }

        $actions[] = GridDataHelper::generateDeleteButton(
            route('api.coupons.destroy', $record['id']),
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

        $addFilters = [
            'status' => $status
        ];
        $addFilters['status']['options']['value'] = UserSettings::get($this->user, 'coupons_filter_status');

        return array_merge($defaultFilters, $addFilters);
    }
}
