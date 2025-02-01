<?php

return [
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID',''),
        'client_secret' => env('PAYPAL_CLIENT_SECRET',''),
        'payment_url' => env('PAYPAL_PAYMENT_URL',''),
    ]
];