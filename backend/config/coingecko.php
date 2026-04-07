<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CoinGecko API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the CoinGecko REST API integration.
    | The API key is used for authenticated requests to avoid rate limiting.
    | In production, this should be injected via secrets management.
    |
    */

    'api_key' => env('COINGECKO_API_KEY', ''),

    'base_url' => env('COINGECKO_BASE_URL', 'https://api.coingecko.com/api/v3'),

    'timeout' => env('COINGECKO_TIMEOUT', 10),

];
