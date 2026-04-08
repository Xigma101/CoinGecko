# Backend Plan (Laravel)

> Laravel API that proxies requests to CoinGecko, keeping the API key secure server-side.
> DDD structure: `app/Domains/Crypto/`

---

## Project Setup

- [x] Scaffold new Laravel project (`composer create-project laravel/laravel backend`)
- [x] Configure `.env` with `COINGECKO_API_KEY` and `COINGECKO_BASE_URL`
- [x] Create `config/coingecko.php` to read env vars cleanly
- [x] Configure CORS (`config/cors.php`) to allow Nuxt frontend origin
- [x] Remove unused default migrations/models (no DB needed)
- [x] Clean out unused scaffold (app/Http, app/Models, database, resources, vite)

## CoinGecko Service

- [x] Create `App\Domains\Crypto\Services\CoinGeckoService`
  - Uses Laravel `Http` facade for requests
  - Injects API key via config, never hardcoded
- [x] Method: `getTopCoins(int $perPage = 10, string $currency = 'usd')`
  - Calls `GET /coins/markets?vs_currency={currency}&order=market_cap_desc&per_page={perPage}&page=1`
- [x] Method: `getCoinDetail(string $id)`
  - Calls `GET /coins/{id}?localization=false&tickers=false&community_data=false&developer_data=false`
- [x] Method: `searchCoins(string $query)`
  - Calls `GET /search?query={query}`
  - Returns filtered results (coins only, matched by name/symbol)
- [x] Error handling:
  - Timeout handling (CoinGecko can be slow)
  - Rate limit detection (HTTP 429)
  - Invalid/empty response handling
  - Wrap in try/catch, return structured error responses
- [x] Register service as singleton in `AppServiceProvider`

## API Routes & Controller

- [x] `GET /api/cryptocurrencies` -- top 10 by market cap
- [x] `GET /api/cryptocurrencies/search?q={query}` -- search by name/symbol
- [x] `GET /api/cryptocurrencies/{id}` -- detailed coin info
- [x] Create `CryptocurrencyController` (DDD: `Domains/Crypto/Http/Controllers/`)
  - `index()` -- calls `getTopCoins()`
  - `show(string $id)` -- calls `getCoinDetail()`
  - `search(SearchCryptoRequest $request)` -- validated via FormRequest
- [x] `SearchCryptoRequest` FormRequest for search validation (DDD: `Domains/Crypto/Http/Requests/`)
- [x] Routes defined in `routes/api.php`
- [x] Rate limiting middleware on API routes (throttle:100,1)

## Response Format

- [x] Consistent JSON structure: `{success, data}` / `{success, message}`
- [x] Appropriate HTTP status codes: 200, 404, 422, 500, 503

## Quality Checklist

- [x] Docblocks on all public service methods
- [x] Docblocks on controller methods
- [x] No API key leakage in responses or logs
- [x] Clean, minimal JSON responses (no unnecessary CoinGecko data forwarded)
- [x] Input validation on search query parameter (FormRequest)

## Test Coverage

- [x] Unit tests: `CoinGeckoServiceTest` (8 tests)
- [x] Feature tests: `CryptocurrencyEndpointTest` (8 tests)
- [x] All 16 tests passing (37 assertions)
