<?php

declare(strict_types=1);

return [
    // Base URL for the FX rates provider
    'base_url' => env('EXCHANGE_RATE_BASE_URL', 'https://api.exchangerate.host'),

    // Access key for the provider
    'access_key' => env('EXCHANGE_RATE_ACCESS_KEY', ''),

    // Default source currency
    'source' => env('EXCHANGE_RATE_SOURCE', 'GHS'),

    // Cache key prefix to store fetched quotes
    'cache_prefix' => env('EXCHANGE_RATE_CACHE_PREFIX', 'fx:rates'),

    // Request timeout in seconds
    'timeout' => env('EXCHANGE_RATE_TIMEOUT', 15),
];
