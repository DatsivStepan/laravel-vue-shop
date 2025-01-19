<?php

use App\Classes\Enum\NotificationsKeys;

return [
    'order_completed' => [
        'body' => "Congratulations <b>{{user_name}}</b>, Your Order Success",
        'status' => 1,
        'subject' => 'Order Completed',
    ],
    'product_available' => [
        'body' => "Dear <b>{{user_full_name}}</b>, product <b>{{product}}</b> available based on your waitlist position",
        'status' => 1,
        'subject' => 'Product available based on your waitlist position',
    ],
    NotificationsKeys::NEW_REVIEW => [
        'body' => "Hello, user {{sender_full_name}} sent review to your product",
        'status' => 1,
        'subject' => 'new review notifications',
    ],
    NotificationsKeys::NEW_COMMENT_REVIEW => [
        'body' => "Hello, user {{sender_full_name}} sent comment to your product",
        'status' => 1,
        'subject' => 'new comment notifications',
    ],
    NotificationsKeys::ORDER_REJECTED => [
        'body' => "Dear <b>{{user_name}}</b>, Your Order Rejected",
        'status' => 1,
        'subject' => 'Order Rejected',
    ],
    NotificationsKeys::INVOICE_MESSAGE => [
        'body' => "Dear <b>{{user_name}}</b>, Sent invoice {{order_id}}",
        'status' => 1,
        'subject' => 'Sent invoice notification message',
    ],
    'notifications' => 'Notifications',
    'edit' => 'Edit Notification',
    'tags' => 'Tags',
    'form' => [
        'subject' => 'Subject',
        'status' => 'Status',
        'body' => 'Body',
    ],
];
