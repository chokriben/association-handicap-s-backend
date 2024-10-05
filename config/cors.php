<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register', 'logout', 'storage/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    'allowed_origins' => ['http://localhost:3000'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', '*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];

