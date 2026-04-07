<?php

namespace App\Domains\Crypto\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

/**
 * Service for interacting with the CoinGecko REST API.
 *
 * Acts as a proxy layer so the API key remains server-side
 * and is never exposed to the frontend client.
 */
class CoinGeckoService
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('coingecko.base_url');
        $this->apiKey = config('coingecko.api_key');
        $this->timeout = config('coingecko.timeout');
    }

    /**
     * Fetch the top cryptocurrencies ranked by market cap.
     *
     * @param int $perPage Number of results to return (default 10)
     * @param string $currency The target currency for price data (default 'usd')
     * @return array
     *
     * @throws \Exception
     */
    public function getTopCoins(int $perPage = 10, string $currency = 'usd'): array
    {
        $response = $this->request('/coins/markets', [
            'vs_currency' => $currency,
            'order' => 'market_cap_desc',
            'per_page' => $perPage,
            'page' => 1,
            'sparkline' => false,
        ]);

        return $response;
    }

    /**
     * Fetch detailed information for a specific cryptocurrency.
     *
     * @param string $id The CoinGecko coin ID (e.g. 'bitcoin', 'ethereum')
     * @return array
     *
     * @throws \Exception
     */
    public function getCoinDetail(string $id): array
    {
        $response = $this->request("/coins/{$id}", [
            'localization' => 'false',
            'tickers' => 'false',
            'community_data' => 'false',
            'developer_data' => 'false',
        ]);

        return $response;
    }

    /**
     * Search for cryptocurrencies by name or symbol.
     *
     * @param string $query The search term (e.g. 'bitcoin' or 'btc')
     * @return array Filtered list of matching coins
     *
     * @throws \Exception
     */
    public function searchCoins(string $query): array
    {
        $response = $this->request('/search', [
            'query' => $query,
        ]);

        // The /search endpoint returns multiple categories; we only need coins
        return $response['coins'] ?? [];
    }

    /**
     * Make an authenticated request to the CoinGecko API.
     *
     * Handles timeouts, rate limiting (429), and general errors
     * with structured exception messages.
     *
     * @param string $endpoint The API endpoint path
     * @param array $params Query parameters
     * @return array Decoded JSON response
     *
     * @throws \Exception
     */
    private function request(string $endpoint, array $params = []): array
    {
        try {
            $response = Http::baseUrl($this->baseUrl)
                ->timeout($this->timeout)
                ->withHeaders($this->buildHeaders())
                ->get($endpoint, $params);

            if ($response->status() === 429) {
                throw new \Exception('CoinGecko API rate limit exceeded. Please try again shortly.', 429);
            }

            if ($response->status() === 404) {
                throw new \Exception('The requested cryptocurrency was not found.', 404);
            }

            $response->throw();

            return $response->json();
        } catch (ConnectionException $e) {
            throw new \Exception('Unable to connect to CoinGecko API. Please try again later.', 503);
        } catch (RequestException $e) {
            throw new \Exception(
                'CoinGecko API request failed: ' . $e->getMessage(),
                $e->response?->status() ?? 500
            );
        }
    }

    /**
     * Build request headers, including the API key if configured.
     *
     * @return array
     */
    private function buildHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        if (!empty($this->apiKey)) {
            $headers['x-cg-demo-api-key'] = $this->apiKey;
        }

        return $headers;
    }
}
