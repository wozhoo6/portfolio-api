<?php


return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    // FRONTEND_URL="https://a.com,http://localhost:3000" -> ['https://a.com', 'http://localhost:3000']
    'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', env('FRONTEND_URL', ''))))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'Accept', 'X-Admin-Token', 'X-Requested-With'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];