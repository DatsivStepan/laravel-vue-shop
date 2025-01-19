<?php

namespace App\Http\Resources\Admin;

use App\Classes\Enum\Order\BillingType;
use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Order\PaymentType;
use App\Helpers\GridDataHelper;
use App\Models\Orders;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class InvoicesCollection extends ResourceCollection
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
                'options' => []
            ]
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
        return [
            'id' => [
                'value' => __('orders.sales_table.invoice_id'),
                'sort' => true
            ],
            'customer' => [
                'value' => __('orders.sales_table.customer'),
                'sort' => true
            ],
            'products' => __('orders.sales_table.products'),
            'created_at' => [
                'value' => __('orders.sales_table.date_issued'),
                'sort' => true
            ],
            'amount' => [
                'value' => __('orders.sales_table.balance'),
                'sort' => true
            ],
            'payment_status' => [
                'value' => __('orders.sales_table.status'),
                'sort' => true
            ],
            'billing_type' => __('orders.sales_table.billing_type'),

            'actions' => ''
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRowsItems()
    {
        return $this->collection->map(function ($collection) {
            $cResource = (new SalesResource($collection))->toArray(Container::getInstance()->make('request'));

            return [
                'id' => [
                    'value' => $cResource['id']
                ],
                'customer' => [
                    'value' => $cResource['customer']
                ],
                'products' => [
                    'value' => implode(', ', $collection->products()->pluck('products.name')->all())
                ],
                'paid_on' => [
                    'type' => 'datetime',
                    'value' => $cResource['created_at']
                ],
                'amount' => [
                    'value' => $cResource['formatted_amount']
                ],
                'status' => [
                    'value' => $cResource['status']
                ],
                'billing_type' => [
                    'value' => __('products.billing_type.' . $cResource['billing_type']),
                ],
                'actions' => $this->getActions($collection),
                'index' => $collection->id
            ];
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
                'className' => '',
                'placeholder' => __('orders.filters.payment_status'),
                'options' => $statuses,
                'value' => null
            ]),
            'paid_on_between' => GridDataHelper::generateFilter('date_range', [
                'className' => '',
                'placeholder' => __('orders.filters.paid_on'),
                'options' => []
            ])
        ];

        return array_merge($defaultFilters, $addFilters);
    }


    /**
     * @return array
     */
    public function getBulkActions()
    {
        return [
            'url' => '/admin/sales/bulk-actions',
            'actions' => [
                'deleteIn' => __('system.delete')
            ]
        ];
    }

    /**
     * @param $collection
     * @return array
     */
    public function getActions($collection)
    {
        $actions = [];

        if ($collection->isPending() && (getSetting('enable_manual_invoices_approval') || $collection->payment_type === PaymentType::INVOICE)) {
            $actions[] = GridDataHelper::generateActionApi(__('system.approve'),
                route('api.admin.sales.manualApproval', $collection->id));

            $actions[] = GridDataHelper::generateActionApi(__('system.reject'),
                route('api.admin.sales.manualReject', $collection->id));
        }

        if (getSetting('edit_pending_invoices') && $collection->payment_status === PaymentStatus::PAYMENT_STATUS_PENDING &&
            $collection->payment_type === PaymentType::INVOICE &&
            $collection->billing_type === BillingType::REGULAR
        ) {
            $actions[] = GridDataHelper::generateActionLink(
                __('system.edit'),
                'checkout-edit',
                ['order_id' => $collection->id]
            );
        }

        if ($collection->checkPaymentType(PaymentType::INVOICE)) {
            $actions[] = GridDataHelper::generateActionMethod(
                'emit',
                __('system.print'),
                [
                    'method' => 'print-invoice',
                    'id' => $collection->id,
                ]);
        }

        $actions[] = GridDataHelper::generateActionMethod(
            'emit',
            __('orders.resend_invoice'),
            [
                'method' => 'repeat-email',
                'id' => $collection->id,
            ]);

        return $actions;
    }
}
