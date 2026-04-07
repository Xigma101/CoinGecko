# Backend Plan (Laravel)

> Laravel API that proxies requests to CoinGecko, keeping the API key secure server-side.

---

## Project Setup

- [ ] Scaffold new Laravel project (`composer create-project laravel/laravel backend`)
- [ ] Configure `.env` with `COINGECKO_API_KEY` and `COINGECKO_BASE_URL`
- [ ] Create `config/coingecko.php` to read env vars cleanly
- [ ] Configure CORS (`config/cors.php`) to allow Nuxt frontend origin
- [ ] Remove unused default migrations/models (no DB needed)

## CoinGecko Service

- [ ] Create `App\Services\CoinGeckoService`
  - Uses Laravel `Http` facade for requests
  - Injects API key via config, never hardcoded
- [ ] Method: `getTopCoins(int $perPage = 10, string $currency = 'usd')`
  - Calls `GET /coins/markets?vs_currency={currency}&order=market_cap_desc&per_page={perPage}&page=1`
- [ ] Method: `getCoinDetail(string $id)`
  - Calls `GET /coins/{id}?localization=false&tickers=false&community_data=false&developer_data=false`
- [ ] Method: `searchCoins(string $query)`
  - Calls `GET /search?query={query}`
  - Returns filtered results (coins only, matched by name/symbol)
- [ ] Error handling:
  - Timeout handling (CoinGecko can be slow)
  - Rate limit detection (HTTP 429)
  - Invalid/empty response handling
  - Wrap in try/catch, return structured error responses
- [ ] Register service in `AppServiceProvider` for dependency injection

## API Routes & Controller

- [ ] `GET /api/cryptocurrencies` -- top 10 by market cap
- [ ] `GET /api/cryptocurrencies/search?q={query}` -- search by name/symbol
- [ ] `GET /api/cryptocurrencies/{id}` -- detailed coin info
- [ ] Create `CryptocurrencyController` with:
  - `index()` -- calls `getTopCoins()`
  - `show(string $id)` -- calls `getCoinDetail()`
  - `search(Request $request)` -- validates `q` param, calls `searchCoins()`
- [ ] Routes defined in `routes/api.php`
- [ ] Rate limiting middleware on API routes (throttle)

## Response Format

- [ ] Consistent JSON structure:
  ```json
  {
    "success": true,
    "data": { ... }
  }
  ```
  or on error:
  ```json
  {
    "success": false,
    "message": "Error description"
  }
  ```
- [ ] Appropriate HTTP status codes: 200, 404, 422, 500, 503

## Quality Checklist

- [ ] Docblocks on all public service methods
- [ ] Docblocks on controller methods
- [ ] No API key leakage in responses or logs
- [ ] Clean, minimal JSON responses (no unnecessary CoinGecko data forwarded)
- [ ] Input validation on search query parameter
