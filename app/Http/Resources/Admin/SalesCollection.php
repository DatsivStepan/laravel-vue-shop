<?php

namespace App\Http\Resources\Admin;

use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Settings\RefundSettings;
use App\Helpers\GridDataHelper;
use App\Models\Orders;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class SalesCollection extends ResourceCollection
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
                'bulkActions' => $this->getBulkActions(),
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
        $columns = [];

        $columns['id'] = [
            'value' => __('orders.sales_table.id'),
            'sort' => true,
        ];
        $columns['customer'] = [
            'value' => __('orders.sales_table.customer'),
            'sort' => true,
        ];
        $columns['products'] = __('orders.sales_table.products');
        $columns['created_at'] = [
            'value' => __('orders.sales_table.paid_on'),
            'sort' => true,
        ];
        $columns['amount'] = [
            'value' => __('orders.sales_table.amount'),
            'sort' => true,
        ];
        $columns['subtotal'] = [
            'value' => __('orders.sales_table.total'),
            'sort' => true,
        ];
        $columns['discount'] = [
            'value' => __('orders.sales_table.discount'),
            'sort' => true,
        ];

        if (getSetting('display_coupons_on_sales')) {
            $columns['coupons'] = __('orders.sales_table.coupons');
        }

        if (getSetting('enable_taxes')) {
            $columns['taxes'] = __('orders.sales_table.taxes');
        }

        if (getSetting('enable_refund')) {
            $columns['refunded'] = __('orders.refunds.refunded');
        }

        $columns['payment_status'] = [
            'value' => __('orders.sales_table.status'),
            'sort' => true,
        ];
        $columns['quantity'] = __('orders.sales_table.quantity');
        $columns['billing_type'] = __('orders.sales_table.billing_type');
        $columns['payment_type'] = __('orders.sales_table.payment_type');
        $columns['note'] = [
            'value' => __('orders.sales_table.notes'),
            'sort' => true,
        ];
        $columns['actions'] = '';


        return $columns;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRowsItems(): \Illuminate\Support\Collection
    {
        $reportCurrency = getSetting('display_user_currency_in_reports') ? null : getSetting('currency');
        return $this->collection->map(function ($collection) use ($reportCurrency) {
            $cResource = (new SalesResource($collection,
                $reportCurrency))->toArray(Container::getInstance()->make('request'));

            $row = [];
            $row['id'] = ['value' => $cResource['id'],];
            $row['customer'] = ['value' => $cResource['customer'],];
            $row['products'] = [
                'value' => implode(', ', $collection->products()->pluck('products.name')->all()),
            ];
            $row['paid_on'] = [
                'type' => 'datetime',
                'value' => $cResource['created_at'],
            ];
            $row['amount'] = [
                'value' => $cResource['formatted_amount'],
            ];

            $row['total'] = [
                'value' => $cResource['formatted_subtotal'],
            ];

            $row['discount'] = [
                'value' => $cResource['formatted_discount'],
            ];

            if (getSetting('display_coupons_on_sales')) {
                $row['coupons'] = [
                    'value' => $cResource['coupons'],
                ];
            }

            if (getSetting('enable_taxes')) {
                $row['taxes'] = [
                    'value' => $cResource['formatted_tax'],
                ];
            }

            if (getSetting('enable_refund')) {
                $row['refunded'] = [
                    'value' => $cResource['refunded'] ? $cResource['formatted_refunded'] : '-',
                ];
            }

            $row['status'] = [
                'value' => $cResource['status'],
            ];

            $row['quantity'] = [
                'value' => $collection->getProductsCount(),
            ];
            $row['billing_type'] = [
                'value' => $cResource['billing_type'],
            ];
            $row['payment_type'] = [
                'value' => $cResource['payment_type'],
            ];
            $row['notes'] = [
                'value' => $cResource['note'],
            ];
            $row['actions'] = $this->getActions($collection, $cResource['refunded']);
            $row['index'] = $collection->id;

            return $row;
        });
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/sales/bulk-actions',
            'actions' => [
                'deleteIn' => __('system.delete'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $filters = GridDataHelper::getDefaultFilters();
        unset($filters['visible']);

        $statusOptions = Arr::prepend(
            Orders::getStatuses(),
            __('system.show_all'),
            ''
        );
        $filters['payment_status'] = GridDataHelper::generateFilter('select', [
            'className' => '',
            'placeholder' => __('system.status'),
            'options' => $statusOptions,
            'value' => null,
        ]);

        $filters['paid_on_between'] = GridDataHelper::generateFilter('date_range', [
            'className' => '',
            'placeholder' => __('orders.filters.paid_on'),
            'options' => [],
        ]);

        $billingType = Arr::prepend(
            Orders::getBillingTypes(),
            __('system.show_all'),
            ''
        );
        $filters['billing_type'] = GridDataHelper::generateFilter('select', [
            'className' => '',
            'placeholder' => __('system.billing_type'),
            'options' => $billingType,
            'value' => null,
        ]);


        return $filters;
    }

    /**
     * @param $collection
     * @param $refunded
     * @return array
     */
    public function getActions($collection, $refunded)
    {
        $actions = [];

        if (getSetting(RefundSettings::ENABLE_REFUND) && ($collection->payment_status === PaymentStatus::PAYMENT_STATUS_COMPLETED || $collection->payment_status === PaymentStatus::PAYMENT_STATUS_REFUNDED_PARTIAL) && $collection->amount > $refunded) {
            $actions[] = GridDataHelper::generateActionLink(
                __('orders.refund'),
                'refund',
                ['order_id' => $collection->id]
            );
        }

        $actions[] = GridDataHelper::generateDeleteButton(
            '/admin/sales/' . $collection->id,
            __('system.delete')
        );

        return $actions;
    }
}
