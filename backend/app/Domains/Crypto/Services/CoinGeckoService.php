<?php

namespace App\Domains\Crypto\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

/**
 * Service for interacting with the CoinGecko REST API.
 *
 * Acts as a proxy layer so the API key remains server-side
 * and is never exposed to the frontend client.
 *
 * All responses are cached via Redis to reduce API calls
 * and improve response times for end users.
 */
class CoinGeckoService
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    /** Cache TTLs in seconds */
    private const CACHE_TTL_MARKETS  = 60;   // Top coins: 1 minute (price-sensitive)
    private const CACHE_TTL_DETAIL   = 60;   // Coin detail: 1 minute
    private const CACHE_TTL_SEARCH   = 300;  // Search: 5 minutes (stable data)
    private const CACHE_TTL_TRENDING = 300;  // Trending: 5 minutes
    private const CACHE_TTL_CHART    = 300;  // Chart: 5 minutes (historical)

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
        $cacheKey = "coingecko:markets:{$currency}:{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_TTL_MARKETS, function () use ($perPage, $currency) {
            return $this->request('/coins/markets', [
                'vs_currency' => $currency,
                'order' => 'market_cap_desc',
                'per_page' => $perPage,
                'page' => 1,
                'sparkline' => false,
            ]);
        });
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
        $cacheKey = "coingecko:detail:{$id}";

        return Cache::remember($cacheKey, self::CACHE_TTL_DETAIL, function () use ($id) {
            return $this->request("/coins/{$id}", [
                'localization' => 'false',
                'tickers' => 'true',
                'community_data' => 'false',
                'developer_data' => 'false',
            ]);
        });
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
        $cacheKey = "coingecko:search:" . strtolower($query);

        return Cache::remember($cacheKey, self::CACHE_TTL_SEARCH, function () use ($query) {
            $response = $this->request('/search', [
                'query' => $query,
            ]);

            return $response['coins'] ?? [];
        });
    }

    /**
     * Fetch trending coins, NFTs, and categories from CoinGecko.
     *
     * @return array Trending coins list
     *
     * @throws \Exception
     */
    public function getTrending(): array
    {
        return Cache::remember('coingecko:trending', self::CACHE_TTL_TRENDING, function () {
            $response = $this->request('/search/trending');

            return $response['coins'] ?? [];
        });
    }

    /**
     * Fetch historical market chart data for a specific cryptocurrency.
     *
     * @param string $id       The CoinGecko coin ID (e.g. 'bitcoin')
     * @param string $currency Target currency (e.g. 'usd')
     * @param string $days     Number of days of data (e.g. '1', '7', '30', '90', '365')
     * @return array
     *
     * @throws \Exception
     */
    public function getMarketChart(string $id, string $currency = 'usd', string $days = '7'): array
    {
        $cacheKey = "coingecko:chart:{$id}:{$currency}:{$days}";

        return Cache::remember($cacheKey, self::CACHE_TTL_CHART, function () use ($id, $currency, $days) {
            return $this->request("/coins/{$id}/market_chart", [
                'vs_currency' => $currency,
                'days' => $days,
                'precision' => 'full',
            ]);
        });
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
