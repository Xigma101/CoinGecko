<?php

use App\Domains\Crypto\Http\Controllers\CryptocurrencyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Cryptocurrency endpoints that proxy to the CoinGecko API.
| All routes are prefixed with /api automatically.
|
*/

Route::prefix('cryptocurrencies')->middleware('throttle:100,1')->group(function () {
    Route::get('/', [CryptocurrencyController::class, 'index']);
    Route::get('/search', [CryptocurrencyController::class, 'search']);
    Route::get('/{id}', [CryptocurrencyController::class, 'show']);
});
