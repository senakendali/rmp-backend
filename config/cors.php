<?php

return [
    'paths' => ['*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://rmp-manufacture-frontend-6pzh.vercel.app',
        'http://localhost:3000',
        'http://localhost:5173',
        'http://127.0.0.1:8000',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];

