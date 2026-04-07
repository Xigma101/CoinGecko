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
