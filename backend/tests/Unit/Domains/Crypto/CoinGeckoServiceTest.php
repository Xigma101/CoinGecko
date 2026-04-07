<?php

namespace Tests\Unit\Domains\Crypto;

use App\Domains\Crypto\Services\CoinGeckoService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CoinGeckoServiceTest extends TestCase
{
    private CoinGeckoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CoinGeckoService();
    }

    // ----- getTopCoins -----

    public function test_get_top_coins_returns_array(): void
    {
        Http::fake([
            '*/coins/markets*' => Http::response([
                ['id' => 'bitcoin', 'symbol' => 'btc', 'name' => 'Bitcoin', 'current_price' => 69000],
                ['id' => 'ethereum', 'symbol' => 'eth', 'name' => 'Ethereum', 'current_price' => 3500],
            ]),
        ]);

        $result = $this->service->getTopCoins();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('bitcoin', $result[0]['id']);
    }

    public function test_get_top_coins_respects_per_page_parameter(): void
    {
        Http::fake([
            '*/coins/markets*' => Http::response([
                ['id' => 'bitcoin', 'symbol' => 'btc', 'name' => 'Bitcoin'],
            ]),
        ]);

        $this->service->getTopCoins(5);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'per_page=5');
        });
    }

    public function test_get_top_coins_passes_currency_parameter(): void
    {
        Http::fake([
            '*/coins/markets*' => Http::response([]),
        ]);

        $this->service->getTopCoins(10, 'eur');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'vs_currency=eur');
        });
    }

    // ----- getCoinDetail -----

    public function test_get_coin_detail_returns_coin_data(): void
    {
        Http::fake([
            '*/coins/bitcoin*' => Http::response([
                'id' => 'bitcoin',
                'symbol' => 'btc',
                'name' => 'Bitcoin',
                'market_data' => ['current_price' => ['usd' => 69000]],
            ]),
        ]);

        $result = $this->service->getCoinDetail('bitcoin');

        $this->assertEquals('bitcoin', $result['id']);
        $this->assertArrayHasKey('market_data', $result);
    }

    public function test_get_coin_detail_throws_on_not_found(): void
    {
        Http::fake([
            '*/coins/invalid-coin*' => Http::response([], 404),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(404);

        $this->service->getCoinDetail('invalid-coin');
    }

    // ----- searchCoins -----

    public function test_search_coins_returns_coins_array(): void
    {
        Http::fake([
            '*/search*' => Http::response([
                'coins' => [
                    ['id' => 'bitcoin', 'name' => 'Bitcoin', 'symbol' => 'BTC'],
                    ['id' => 'bitcoin-cash', 'name' => 'Bitcoin Cash', 'symbol' => 'BCH'],
                ],
                'exchanges' => [],
                'categories' => [],
            ]),
        ]);

        $result = $this->service->searchCoins('bitcoin');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('bitcoin', $result[0]['id']);
    }

    public function test_search_coins_returns_empty_when_no_coins_key(): void
    {
        Http::fake([
            '*/search*' => Http::response(['exchanges' => []]),
        ]);

        $result = $this->service->searchCoins('xyz');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // ----- getTrending -----

    public function test_get_trending_returns_coins_array(): void
    {
        Http::fake([
            '*/search/trending*' => Http::response([
                'coins' => [
                    ['item' => ['id' => 'pepe', 'name' => 'Pepe', 'symbol' => 'PEPE']],
                    ['item' => ['id' => 'bonk', 'name' => 'Bonk', 'symbol' => 'BONK']],
                ],
                'nfts' => [],
            ]),
        ]);

        $result = $this->service->getTrending();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('pepe', $result[0]['item']['id']);
    }

    public function test_get_trending_returns_empty_when_no_coins_key(): void
    {
        Http::fake([
            '*/search/trending*' => Http::response(['nfts' => []]),
        ]);

        $result = $this->service->getTrending();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // ----- getMarketChart -----

    public function test_get_market_chart_returns_price_data(): void
    {
        Http::fake([
            '*/coins/bitcoin/market_chart*' => Http::response([
                'prices' => [[1700000000000, 69000], [1700003600000, 69500]],
                'market_caps' => [[1700000000000, 1350000000000]],
                'total_volumes' => [[1700000000000, 25000000000]],
            ]),
        ]);

        $result = $this->service->getMarketChart('bitcoin');

        $this->assertArrayHasKey('prices', $result);
        $this->assertCount(2, $result['prices']);
    }

    public function test_get_market_chart_passes_currency_and_days(): void
    {
        Http::fake([
            '*/coins/bitcoin/market_chart*' => Http::response([
                'prices' => [],
                'market_caps' => [],
                'total_volumes' => [],
            ]),
        ]);

        $this->service->getMarketChart('bitcoin', 'eur', '30');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'vs_currency=eur')
                && str_contains($request->url(), 'days=30');
        });
    }

    public function test_get_market_chart_uses_full_precision(): void
    {
        Http::fake([
            '*/coins/bitcoin/market_chart*' => Http::response([
                'prices' => [],
                'market_caps' => [],
                'total_volumes' => [],
            ]),
        ]);

        $this->service->getMarketChart('bitcoin');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'precision=full');
        });
    }

    public function test_get_market_chart_throws_on_not_found(): void
    {
        Http::fake([
            '*/coins/invalid-coin/market_chart*' => Http::response([], 404),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(404);

        $this->service->getMarketChart('invalid-coin');
    }

    // ----- Error handling -----

    public function test_throws_on_rate_limit(): void
    {
        Http::fake([
            '*/coins/markets*' => Http::response([], 429),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(429);

        $this->service->getTopCoins();
    }

    public function test_throws_on_connection_error(): void
    {
        Http::fake([
            '*/coins/markets*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
            },
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(503);

        $this->service->getTopCoins();
    }
}
