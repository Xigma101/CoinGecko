<?php

namespace App\Domains\Crypto\Http\Controllers;

use App\Domains\Crypto\Http\Requests\SearchCryptoRequest;
use App\Domains\Crypto\Services\CoinGeckoService;
use Illuminate\Http\JsonResponse;

/**
 * Handles cryptocurrency API endpoints.
 *
 * Proxies requests to the CoinGecko API via CoinGeckoService,
 * returning structured JSON responses to the frontend.
 */
class CryptocurrencyController
{
    public function __construct(
        private CoinGeckoService $coinGeckoService
    ) {}

    /**
     * Get the top 10 cryptocurrencies by market cap.
     *
     * GET /api/cryptocurrencies
     */
    public function index(): JsonResponse
    {
        try {
            $coins = $this->coinGeckoService->getTopCoins();

            return response()->json([
                'success' => true,
                'data' => $coins,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Get detailed information for a specific cryptocurrency.
     *
     * GET /api/cryptocurrencies/{id}
     *
     * @param string $id CoinGecko coin ID (e.g. 'bitcoin')
     */
    public function show(string $id): JsonResponse
    {
        try {
            $coin = $this->coinGeckoService->getCoinDetail($id);

            return response()->json([
                'success' => true,
                'data' => $coin,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Search for cryptocurrencies by name or symbol.
     *
     * GET /api/cryptocurrencies/search?q={query}
     */
    public function search(SearchCryptoRequest $request): JsonResponse
    {
        try {
            $results = $this->coinGeckoService->searchCoins($request->validated()['q']);

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Build a consistent error response from an exception.
     */
    private function errorResponse(\Exception $e): JsonResponse
    {
        $status = $e->getCode();

        // Ensure we return a valid HTTP status code
        if ($status < 400 || $status > 599) {
            $status = 500;
        }

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], $status);
    }
}
