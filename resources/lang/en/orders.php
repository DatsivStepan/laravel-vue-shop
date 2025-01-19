<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Role and Orders Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'orders' => 'Orders',
    'total' => 'Total',
    'price' => 'Price',
    'discount_price' => 'Discount Price',
    'my_orders' => 'My orders',
    'discount' => 'Discount',
    'remove' => 'Remove',
    'proceedtocheckout' => 'Proceed to checkout',
    'orderempty' => 'Order empty',
    'paymentmethods' => 'Payment methods',
    'processpayment' => 'Process Payment',
    'invoicetext' => 'If you choose to pay by invoice, download, process, and inform the company representative to expect your payment.',
    'cost' => 'Cost',
    'id' => 'ID',
    'name' => 'Name',
    'sales' => 'Sales',
    'note' => 'Note',
    'status' => 'Status',
    'refund' => 'Refund',
    'all_sales' => 'All sales',
    'invoices' => 'Invoices',
    'shipping' => 'Shipping',
    'invoice' => 'Invoice',
    'balance' => 'Balance',
    'customer' => 'Customer',
    'questionremovefromcart' => 'Are you sure you want delete?',
    'startshopping' => 'Start Shopping',
    'yourcartisempty' => 'Your cart is currently empty',
    'youmustaddproduct' => 'Before proceed to checkout you must add some products to your shopping cart. You will find a lot of interesting products on our "Shop" page.',
    'tab_options_title' => 'Options',
    'tab_product_title' => 'Products',
    'tabcustomertitle' => 'Customer Info',
    'tabcustomersubtitle' => 'Name and address',
    'tabshippingtitle' => 'Shipping',
    'tabshippingsubtitle' => 'Delivery address',
    'tabpaymenttitle' => 'Payment',
    'tabpaymentsubtitle' => 'Payment details',
    'quantity' => 'Quantity',
    'ordersummary' => 'Order summary',
    'paginationbackto' => 'Back to ',
    'paginationprocessto' => 'Process to ',
    'backtocatalog' => 'Back to catalog',
    'congratulations' => 'Congratulations',
    'orderaccepted' => 'Your order is accepted',
    'orderacceptedtext' => 'Thank you for your order! Your order is being processed and will be completed within 3-6 hours. You will receive an email confirmation when your order is completed.',
    'continueshopping' => 'Continue Shopping',
    'billto' => 'Bill to',
    'date' => 'Date',
    'balancedue' => 'Balance due',
    'subtotal' => 'Sub-Total',
    'i_product ' => 'Product',
    'i_quantity ' => 'Quantity',
    'i_price ' => 'Price',
    'i_amount' => 'Amount',
    'noorder' => 'No find Order',
    'remove' => 'Remove',
    'request_messages' => [
        'added_to_cart' => 'Product added to cart.',
    ],
    'have_coupon' => 'Have a coupon?',
    'apply_coupon' => 'Apply your coupon below.',
    'errors' => [
        'no_seats' => 'Maximum seats number reached: ":SEAT_V".',
        'no_product' => 'No product',
        'product_already_added' => 'The product is already added',
        'no_vendor_seats' => 'Maximum seats number reached: ":SEAT_V". Already used: ":USED" seats.',
        'coupon_already_added' => 'This coupon already added in order',
        'has_not_coupon' => 'Order has not this coupon',
        'amount_is_more_than_allowable' => 'the amount is more than the allowable',
    ],
    'status' => [
        'in_cart' => 'In cart',
        'pending' => 'Pending',
        'completed' => 'Completed',
        'refund' => 'Refund',
        'refunded_partial' => 'Refunded partial',
        'rejected' => 'Rejected',
    ],
    'sales_table' => [
        'id' => 'Order id',
        'customer' => 'Сustomer',
        'products' => 'Products',
        'paid_on' => 'Paid on',
        'amount' => 'Amount',
        'total' => 'Total',
        'discount' => 'Discount',
        'taxes' => 'Taxes',
        'coupons' => 'Coupons',
        'status' => 'Status',
        'quantity' => 'Quantity',
        'billing_type' => 'Billing type',
        'payment_type' => 'Payment type',
        'notes' => 'Notes',
        'date_issued' => 'Date issued',
        'balance' => 'Balance',
        'invoice_id' => 'Invoice #',
    ],
    'filters' => [
        'payment_type' => 'Select payment type',
        'payment_status' => 'Status',
        'paid_on' => 'Paid on',
        'show_all' => 'Show all'
    ],
    'resend_invoice' => 'Resend invoice',
    'repeat_email_sended' => 'Repeat email sended!',

    'installments' => [
        'recurring_period' => 'Recurring period',
        'billing_cycles' => 'Billing cycles',
        'price' => 'Installment price',
        'fee' => 'fee',
    ],

    'pdf_invoice' => [
        'table' => [
            'product' => 'Product',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'currency' => 'Currency',
            'discount' => 'Discount',
            'tax' => 'Tax',
            'amount' => 'Amount',
        ],
        'invoice' => 'INVOICE',
        'bill_to' => 'BILL TO',
        'date' => 'DATE',
        'subtotal' => 'SUBTOTAL',
        'discount' => 'DISCOUNT',
        'tax' => 'TAX',
        'balance_due' => 'BALANCE DUE',
    ],
    'scheduled_enrollment' => [
        'name' => 'Scheduled Enrollments',
        'table' => [
            'user_name' => 'User name',
            'product_name' => 'Product name',
            'purchased' => 'Purchased',
            'enrollment_time' => 'Enrollment time',
            'order_id' => 'Order ID',
            'status' => 'Status'
        ]
    ],
    'seats' => [
        'name' => 'Seats',
        'table' => [
            'user_name' => 'Customer',
            'product_name' => 'Product',
            'key' => 'Key',
            'seats_count' => 'Seats count',
            'seats_used' => 'Seats used',
            'created' => 'Created',
        ],
        'actions' => [
                'details' => 'Seats Details'
        ]
    ],
    'seats_details' => [
        'name' => 'Seats Details',
        'table' => [
            'user_name' => 'Customer',
            'product_name' => 'Product',
            'created' => 'Date used',
            'status' => 'Status',
        ],
    ],
    'refunds' => [
        'manual_refund' => 'Manual refund',
        'refunded' => 'Refunded',
        'amount' => 'Refund amount',
        'maximum_refund_amount' => 'Maximum refund amount',
        'reason' => 'Reason',
        'refund' => 'Refund',
        'refunded' => 'Refunded',
        'maximum_amount' => 'Maximum amount',
        'confirm_message' => 'You are going to refund :amount from current order?',
    ],
    'products' => [
        'enrolled' => 'Enrolled',
        'deleted' => 'Deleted',
        'pending' => 'Pending',
    ],

    'response_messages' => [
        'payed' => 'Payed',
        'rejected' => 'Rejected',
    ],

    'you_order_is_pending' => 'Your order is pending',
    'you_order_is_pending_description' => 'You will receive an email confirmation when your order is completed',
    'download_invoice' => 'Download Invoice',
];
