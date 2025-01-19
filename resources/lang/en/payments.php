<?php

return [
    "payments" => "Payment types",
    "create_new" => "Add new",
    "create" => "Create payment",
    "edit" => "Edit payment",
    'form' => [
        'name' => 'Name',
        'currency' => 'Currency',
        'type' => 'Type',
        'status' => 'Status',
        //paypal
        'client_id' => 'Client ID',
        'secret' => 'Secret key',
        'mode' => 'Mode',
        //paypal
        'authorize' => [
            'login_id' => 'Login ID',
            'transaction_key' => 'Transaction key',
            'public_client_key' => 'Public client key',
        ],
        'publishable_key' => 'Publishable key'
    ],
    //paypal
    'sandbox' => 'Sandbox',
    'production' => 'Production',
    //paypal

    'methods' => [
        'paypal' => 'PayPal',
        'stripe' => 'Stripe',
        'authorize' => 'Authorize',
    ],
    'webhook' => [
        'url' => 'Webhook url',
    ],
];
