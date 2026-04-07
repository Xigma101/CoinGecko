<?php

namespace Tests\Feature\Domains\Crypto;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CryptocurrencyEndpointTest extends TestCase
{
    private function fakeMarketData(): array
    {
        return [
            [
                'id' => 'bitcoin',
                'symbol' => 'btc',
                'name' => 'Bitcoin',
                'current_price' => 69000,
                'market_cap' => 1350000000000,
                'market_cap_rank' => 1,
                'price_change_percentage_24h' => -1.5,
            ],
            [
                'id' => 'ethereum',
                'symbol' => 'eth',
                'name' => 'Ethereum',
                'current_price' => 3500,
                'market_cap' => 420000000000,
                'market_cap_rank' => 2,
                'price_change_percentage_24h' => 2.3,
            ],
        ];
    }

    // ----- GET /api/cryptocurrencies -----

    public function test_index_returns_top_coins(): void
    {
        Http::fake(['*/coins/markets*' => Http::response($this->fakeMarketData())]);

        $response = $this->getJson('/api/cryptocurrencies');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', 'bitcoin');
    }

    public function test_index_passes_currency_parameter(): void
    {
        Http::fake(['*/coins/markets*' => Http::response([])]);

        $this->getJson('/api/cryptocurrencies?currency=eur');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'vs_currency=eur');
        });
    }

    public function test_index_handles_api_failure(): void
    {
        Http::fake(['*/coins/markets*' => Http::response([], 500)]);

        $response = $this->getJson('/api/cryptocurrencies');

        $response->assertStatus(500)
            ->assertJson(['success' => false]);
    }

    // ----- GET /api/cryptocurrencies/trending -----

    public function test_trending_returns_coins(): void
    {
        Http::fake([
            '*/search/trending*' => Http::response([
                'coins' => [
                    ['item' => ['id' => 'pepe', 'name' => 'Pepe', 'symbol' => 'PEPE']],
                ],
                'nfts' => [],
            ]),
        ]);

        $response = $this->getJson('/api/cryptocurrencies/trending');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(1, 'data');
    }

    public function test_trending_handles_api_failure(): void
    {
        Http::fake(['*/search/trending*' => Http::response([], 500)]);

        $response = $this->getJson('/api/cryptocurrencies/trending');

        $response->assertStatus(500)
            ->assertJson(['success' => false]);
    }

    // ----- GET /api/cryptocurrencies/{id} -----

    public function test_show_returns_coin_detail(): void
    {
        Http::fake([
            '*/coins/bitcoin*' => Http::response([
                'id' => 'bitcoin',
                'symbol' => 'btc',
                'name' => 'Bitcoin',
                'market_data' => ['current_price' => ['usd' => 69000]],
            ]),
        ]);

        $response = $this->getJson('/api/cryptocurrencies/bitcoin');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('data.id', 'bitcoin');
    }

    public function test_show_returns_404_for_invalid_coin(): void
    {
        Http::fake(['*/coins/not-a-coin*' => Http::response([], 404)]);

        $response = $this->getJson('/api/cryptocurrencies/not-a-coin');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    // ----- GET /api/cryptocurrencies/search -----

    public function test_search_returns_matching_coins(): void
    {
        Http::fake([
            '*/search*' => Http::response([
                'coins' => [
                    ['id' => 'bitcoin', 'name' => 'Bitcoin', 'symbol' => 'BTC'],
                ],
            ]),
        ]);

        $response = $this->getJson('/api/cryptocurrencies/search?q=bit');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(1, 'data');
    }

    public function test_search_rejects_missing_query(): void
    {
        $response = $this->getJson('/api/cryptocurrencies/search');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'A search query is required.',
            ]);
    }

    public function test_search_rejects_short_query(): void
    {
        $response = $this->getJson('/api/cryptocurrencies/search?q=b');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Search query must be at least 2 characters.',
            ]);
    }

    public function test_search_handles_api_failure(): void
    {
        Http::fake(['*/search*' => Http::response([], 500)]);

        $response = $this->getJson('/api/cryptocurrencies/search?q=bitcoin');

        $response->assertStatus(500)
            ->assertJson(['success' => false]);
    }

    // ----- GET /api/cryptocurrencies/{id}/market-chart -----

    public function test_market_chart_returns_price_data(): void
    {
        Http::fake([
            '*/coins/bitcoin/market_chart*' => Http::response([
                'prices' => [[1700000000000, 69000], [1700003600000, 69500]],
                'market_caps' => [[1700000000000, 1350000000000]],
                'total_volumes' => [[1700000000000, 25000000000]],
            ]),
        ]);

        $response = $this->getJson('/api/cryptocurrencies/bitcoin/market-chart');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'data.prices');
    }

    public function test_market_chart_accepts_currency_and_days(): void
    {
        Http::fake([
            '*/coins/bitcoin/market_chart*' => Http::response([
                'prices' => [],
                'market_caps' => [],
                'total_volumes' => [],
            ]),
        ]);

        $response = $this->getJson('/api/cryptocurrencies/bitcoin/market-chart?currency=eur&days=30');

        $response->assertOk();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'vs_currency=eur')
                && str_contains($request->url(), 'days=30');
        });
    }

    public function test_market_chart_rejects_invalid_days(): void
    {
        $response = $this->getJson('/api/cryptocurrencies/bitcoin/market-chart?days=999');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Days must be one of: 1, 7, 30, 90, 365.',
            ]);
    }

    public function test_market_chart_rejects_invalid_currency(): void
    {
        $response = $this->getJson('/api/cryptocurrencies/bitcoin/market-chart?currency=xyz');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Unsupported currency.',
            ]);
    }

    public function test_market_chart_handles_api_failure(): void
    {
        Http::fake(['*/coins/bitcoin/market_chart*' => Http::response([], 500)]);

        $response = $this->getJson('/api/cryptocurrencies/bitcoin/market-chart');

        $response->assertStatus(500)
            ->assertJson(['success' => false]);
    }
}
