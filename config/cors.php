<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | You can configure CORS settings for your Laravel application here. You
    | can specify the allowed methods, origins, headers, and more.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'], // Allow all HTTP methods (GET, POST, PUT, DELETE, etc.)

    'allowed_origins' => [
        'https://rmp-manufacture-frontend-6pzh.vercel.app/',
        'localhost:3000',
        'localhost:5173',
        '127.0.0.1:8000',
    ],
    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'Accept',
        'Origin',
        'Referer',
        // Add other headers your application needs here
    ],

    'exposed_headers' => [],
    
    'max_age' => 0,

    'supports_credentials' => true, // If true, allow cookies to be sent

];
