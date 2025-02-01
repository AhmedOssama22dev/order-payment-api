<?php

return [
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'payment_url' => env('PAYPAL_PAYMENT_URL'),
    ]
    ,
    'stripe' => [
        'client_id' => env(strtoupper('STRIPE_CLIENT_ID')),
        'client_secret' => env(strtoupper('STRIPE_CLIENT_SECRET')),
        'payment_url' => env(strtoupper('STRIPE_PAYMENT_URL')),
    ],
    ,'stripes' => [
        'client_id' => env(strtoupper('STRIPES_CLIENT_ID')),
        'client_secret' => env(strtoupper('STRIPES_CLIENT_SECRET')),
        'payment_url' => env(strtoupper('STRIPES_PAYMENT_URL')),
    ],
];