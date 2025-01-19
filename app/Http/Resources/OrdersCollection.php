<?php

namespace App\Http\Resources;

use App\Classes\Enum\Order\PaymentType;
use App\Helpers\GridDataHelper;
use App\Http\Resources\Admin\SalesResource;
use App\Models\Orders;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class OrdersCollection extends ResourceCollection
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
                'filters' => $this->getFilters(),
                'options' => [],
            ],
        ];
    }

    /**
     * @return App\Http\Resources\Admin\CategoryResource
     */
    public function getData()
    {
        return $this->collection->map(function ($collection) {
            return new SalesResource($collection);
        });
    }

    /**
     * @return array
     */
    public function getHeaderItems()
    {
        $items = [];

        $items['id'] = [
            'value' => __('orders.sales_table.id'),
            'sort' => true,
        ];
        $items['customer'] = [
            'value' => __('orders.sales_table.customer'),
            'sort' => true,
        ];
        $items['products'] = __('orders.sales_table.products');
        $items['created_at'] = [
            'value' => __('orders.sales_table.paid_on'),
            'sort' => true,
        ];
        $items['amount'] = [
            'value' => __('orders.sales_table.amount'),
            'sort' => true,
        ];
        $items['subtotal'] = [
            'value' => __('orders.sales_table.total'),
            'sort' => true,
        ];
        $items['discount'] = [
            'value' => __('orders.sales_table.discount'),
            'sort' => true,
        ];

        if (getSetting('enable_taxes')) {
            $items['taxes'] = __('orders.sales_table.taxes');
        }

        $items['payment_status'] = [
            'value' => __('orders.sales_table.status'),
            'sort' => true,
        ];
        $items['quantity'] = [
            'value' => __('orders.sales_table.quantity'),
            'sort' => true,
        ];
        $items['billing_type'] = __('orders.sales_table.billing_type');
        $items['note'] = [
            'value' => __('orders.sales_table.notes'),
            'sort' => true,
        ];

        $items['actions'] = '';


        return $items;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new SalesResource($collection))->toArray(Container::getInstance()->make('request'));

            $items = [];

            $items['id'] = [
                'value' => $cResource['id'],
            ];
            $items['customer'] = [
                'value' => $cResource['customer'],
            ];
            $items['products'] = [
                'value' => implode(', ', $collection->products()->pluck('products.name')->all()),
            ];
            $items['paid_on'] = [
                'type' => 'datetime',
                'value' => $cResource['created_at'],
            ];
            $items['amount'] = [
                'value' => $cResource['formatted_amount'],
            ];
            $items['total'] = [
                'value' => $cResource['formatted_subtotal'],
            ];
            $items['discount'] = [
                'value' => $cResource['formatted_discount'],
            ];

            if (getSetting('enable_taxes')) {
                $items['taxes'] = [
                    'value' => $cResource['formatted_tax'],
                ];
            }

            $items['status'] = [
                'value' => $cResource['status'],
            ];
            $items['quantity'] = [
                'value' => $collection->getProductsCount(),
            ];
            $items['billing_type'] = [
                'value' => $cResource['billing_type'],
            ];
            $items['notes'] = [
                'value' => $cResource['note'],
            ];

            $actions = $this->getActions($collection);
            if (!empty($actions)) {
                $items['actions'] = $actions;
            }

            return $items;
        });
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $defaultFilters = GridDataHelper::getDefaultFilters();
        unset($defaultFilters['visible']);

        $statuses = Arr::prepend(
            Orders::getStatuses(),
            __('orders.filters.show_all'), '');

        $addFilters = [
            'payment_status' => GridDataHelper::generateFilter('select', [
                'className' => 'w-25',
                'placeholder' => __('orders.filters.payment_status'),
                'options' => $statuses,
            ]),
            'paid_on_between' => GridDataHelper::generateFilter('date_range', [
                'className' => 'w-25',
                'placeholder' => __('orders.filters.paid_on'),
                'options' => [],
            ]),
        ];

        return array_merge($defaultFilters, $addFilters);
    }

    /**
     * @param $collection
     * @return array
     */
    public function getActions($collection)
    {
        $actions = [];

        if ($collection->checkPaymentType(PaymentType::INVOICE)) {
            $actions[] = GridDataHelper::generateActionMethod(
                'emit',
                __('system.print'),
                [
                    'method' => 'print-invoice',
                    'id' => $collection->id,
                ]);
        }

        return $actions;
    }
}
