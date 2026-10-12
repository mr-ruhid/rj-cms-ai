<?php

declare(strict_types=1);

return [
    'api_endpoint' => env('OPENROUTER_API_ENDPOINT', 'https://openrouter.ai/api/v1/'),
    'api_key' => env('OPENROUTER_API_KEY'),
    'api_timeout' => env('OPENROUTER_API_TIMEOUT', 120),
    'title' => env('OPENROUTER_API_TITLE', 'laravel-openrouter'),
    'referer' => env('OPENROUTER_API_REFERER', 'https://github.com/moe-mizrak/laravel-openrouter'),
];
