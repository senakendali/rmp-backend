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

    'allowed_origins' => ['*'], // Allow all origins or specify allowed origins
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Allow all headers

    'exposed_headers' => [],
    
    'max_age' => 0,

    'supports_credentials' => true, // If true, allow cookies to be sent

];
