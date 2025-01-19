<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'settings' => 'Settings',
    'savechanges' => 'Save changes',
    'saved' => 'Saved!',
    'configuration_saved' => 'Configuration Saved',

    'product_catalog' => [
        'display_price_filter' => 'Display Catalog Price Filter',
        'display_price_filter_desc' => 'Display a price filter on the catalog page',
        'display_instructor_filter' => 'Display Catalog Instructor Filter',
        'display_instructor_filter_desc' => 'Display a instrurtors filter on the catalog page',
        'display_search_filter' => 'Display Catalog Search Filter',
        'display_search_filter_desc' => 'Display a search filter on the catalog page',
        'products_list_type' => 'Products list type',
        'products_list_type_options' => [
            'combo' => 'Combo',
            'greed_view' => 'Greed view',
            'list_view' => 'List view',
        ],
        'load_more_action' => 'Load more action',
        'load_more_action_options' => [
            'pages' => 'Pages',
            'load_more' => 'Load more',
        ],
        'items_per_page' => 'Display products per page',
        'items_per_page_desc' => 'Display products per page on catalog page',

        'enabled_favorites_products' => 'Enable Wish list',
        'enabled_favorites_products_desc' => 'Enable Wish list',

        'buy_only_one_product' => 'Buy one product at once',
        'buy_only_one_product_desc' => '',

        'send_comment_review_notification' => 'Send new Comment notification message',
        'send_comment_review_notification_desc' => '',

        'send_review_notification' => 'Send Review notification message',
        'send_review_notification_desc' => '',
        'theme' => 'Theme'
    ],
    'product' => [
        'enable_just_released_products' => 'Enable Just Released Products',
        'hide_empty_categories' => 'Hide Empty Categories',
        'enable_featured_products' => 'Enable Featured Products',
        'enable_products_sorting' => 'Enable Products Sorting',
        'enable_products_custom_fields' => 'Enable Products Custom Fields',
        'enable_reviews' => 'Enable Reviews',
        'reviews_limit' => 'Reviews limit',
        'enable_review_comments' => 'Review Comments',
    ],

    'coupons' => [
        'enable_coupons' => 'Enable Coupons',
        'enable_coupons_desc' => 'Enable Coupons',
        'display_coupons_on_sales' => 'Display Coupons on Sales',
        'display_coupons_on_sales_desc' => 'Display Coupons on Sales Page'
    ],

    'sales' => [
        'enable_customer_info' => 'Enable Customer Info',
        'enable_customer_info_desc' => 'Enable Customer info in Checkout',
        'enable_shipping' => 'Enable Shipping',
        'enable_shipping_desc' => 'Enable Shipping tab in Checkout',
        'invoice_image' => 'Invoice image',
        'invoice_image_desc' => 'Image for invoice pdf',
        'enable_multi_currency' => 'Enable Multi Currency',
        'enable_multi_currency_desc' => '',
        'display_user_currency_in_reports' => 'Display User Currency in Reports',
        'display_user_currency_in_reports_desc' => '',
        'display_scheduled_enrollment_list' => 'Display Scheduled Enrollment List',
        'display_scheduled_enrollment_list_desc' => '',
        'display_deleted_scheduled_enrollments' => 'Display Deleted Scheduled Enrollments',
        'display_deleted_scheduled_enrollments_desc' => '',
        'enable_manual_invoices_approval' => 'Enable Manual Invoices Approval',
        'enable_manual_invoices_approval_desc' => '',
    ],

    'invoice' => [
        'enable_invoice' => 'Enable Invoice Payment',
        'attach_receipt' => 'Attach invoice to checkout notification  ',
        'edit_pending_invoices' => 'Edit Pending Invoices',
        'descriptions' => [
            'enable_invoice' => '',
            'attach_receipt' => '',
            'edit_pending_invoices' => '',
        ],
    ],

    'discount' => [
        'enable_discounts' => 'Enable Discounts',
        'enable_discounts_prices' => 'Enable Discounts Prices',
        'enable_discounts_prices_desc' => 'Calculate and display prices with discounts',
        'enable_manual_discount_selection' => 'Enable manual discount selection',
        'enable_manual_discount_selection_desc' => 'Enable manual discount selection'
    ],

    'vendors' => [
        'enable_vendors' => 'Enable Vendors',
        'enable_seats_vendors' => 'Enable Seats Vendors',
        'enable_vendors_filter' => 'Enable Vendors Filter',
    ],

    'waitlist' => [
        'enable_waitlist' => 'Enable Waitlist',
        'waitlist_duration' => 'Waitlist Duration',
    ],

    'sessions' => [
        'enable_sessions' => 'Enable Sessions',
    ],

    'taxes' => [
        'enable_taxes' => 'Enable Taxes',
        'tax_value' => 'Tax Value',
        'taxes_label' => 'Tax Label',
        'enable_advance_taxes' => 'Enable Advanced Taxes',
        'enable_global_tax_value' => 'Enable Global Tax Value',

        'descriptions' => [
            'tax_value' => 'Tax value in percents (0-100)',
            'taxes_label' => 'Default "Tax" if empty',
            'enable_global_tax_value' => 'Use Global Tax Value if User Tax empty',
        ],
    ],

    'hidden' => [
        'connection_id' => 'Connection ID'
    ],

    'categories' => [
        'general' => 'General',
        'product' => 'Product',
        'coupons' => 'Coupons',
        'discounts' => 'Discounts',
        'sales' => 'Sales',
        'invoice' => 'Invoice',
        'vendor' => 'Vendor',
        'sesisons' => 'Sesisons',
        'waitlist' => 'Waitlist',
        'taxes' => 'Taxes',
        'product_catalog' => 'Products catalog',
        'subscriptions' => 'Subscriptions',
        'installment' => 'Installment',
        'refund' => 'Refund',
    ],

    'general' => [
        'currency' => 'Currency',
        'enable_debugging' => 'Enable Debugging',
        'enable_table_sort_order' => 'Enable Table Sort Order',
        'enable_categories_tree' => 'Enable categories tree',
        'enable_categories_tree_desc' => 'Enable categories tree on navigation menu',

        'enable_sales' => 'Enable sales',
        'enable_sales_desc' => 'Enable sales on navigation menu',

        'enable_my_orders' => 'Enable my orders',
        'enable_my_orders_desc' => 'Enable my orders on navigation menu',

        'enable_my_courses' => 'Enable my courses',
        'enable_my_courses_desc' => 'Enable My courses on navigation menu',

        'enable_my_products' => 'Enable my products',
        'enable_my_products_desc' => 'Enable my products on navigation menu',
    ],

    'subscriptions' => [
        'enable' => 'Enable Subscription',
        'suspend_threshold' => 'Subscription suspend threshold',
        'notification_threshold' => 'Subscription notification threshold',
        'enable_subscriptions_coupons' => 'Enable Subscriptions Coupons',
        'show_total_subscription_price_over_whole_period' => 'Show total subscription price over whole period',
        'enable_catalog_subscriptions' => 'Enable Catalog Subscriptions',
        'allow_one_active_catalog_subscription' => 'Allow one active Catalog Subscription',
        'enable_subscriptions_enrollments_action' => 'Enable subscriptions enrollments actions',

        'descriptions' => [
            'enable' => '',
            'suspend_threshold' => 'Default: 10 days',
            'notification_threshold' => 'Default: 3 days',
            'enable_subscriptions_coupons' => '',
            'show_total_subscription_price_over_whole_period' => "Turn on to show total cost of the subscription over product lifetime instead of cost per billing cycle. Doesn't affect the amount billed to the customer. Only affects products with set number of billing cycles (not zero).",
            'enable_catalog_subscriptions' => '',
            'allow_one_active_catalog_subscription' => 'Start date - product setting "Enroll Start Date"',
            'enable_subscriptions_enrollments_action' => '',
        ],
    ],

    'installments' => [
        'enable' => 'Enable Payment Installment',
        'descriptions' => [
            'enable' => 'Default: No',
        ],
    ],

    'refund' => [
        'enable_refund' => 'Enable refunds',
        'enable_manual_refund' => 'Enable manual refunds',
        'refund_reasons' => 'Refund reasons (one per string)',
        'action_after_refund' => 'Action after refund',

        'descriptions' => [
            'enable_refund' => 'Default: No',
            'enable_manual_refund' => 'Default: No',
            'refund_reasons' => 'Default: Empty',
            'action_after_refund' => 'Default: Keep user enrolled',
        ],

        'action_after_refund_options' => [
            'keep_user_enrollment' => 'Keep user enrollment',
            'disable_course_enrollment' => 'Disable course enrollment',
            'unenrol_user_from_course' => 'Unenrol user from course',
        ],
    ],
];
