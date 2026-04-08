# CoinGecko Crypto Dashboard

A cryptocurrency tracking web application that displays real-time market data from the CoinGecko API. Users can browse the top 10 coins by market cap, view detailed information for any cryptocurrency, search by name or symbol, and compare prices across 10 different currencies.

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.4) — API proxy with DDD architecture
- **Frontend:** Nuxt 3 (Vue.js) with server-side rendering
- **Styling:** Tailwind CSS — responsive, mobile-first dark theme
- **Charts:** nuxt-charts (Unovis) — interactive price history
- **Caching:** Redis 7 — response caching with configurable TTLs
- **Images:** @nuxt/image — lazy loading and size optimization
- **Utilities:** @vueuse/nuxt — SSR-safe composables
- **API:** CoinGecko REST API
- **Infrastructure:** Docker Compose (3 services)

## Features

- **Top 10 Cryptocurrencies** — Homepage displays the top coins ranked by market cap with price, 24h change, and volume
- **Sortable Table** — Click column headers to sort by rank, price, 24h change, market cap, or volume
- **Coin Detail Pages** — Click any coin for detailed stats including price, market cap, volume, supply, 24h high/low, and description
- **Price History Charts** — Interactive line charts with selectable time periods (24H, 7D, 30D, 90D, 1Y)
- **Trading Tickers** — Live exchange pair data with spread indicators, auto-refreshing every 60 seconds
- **Search** — Debounced search bar with dropdown results, pre-loaded trending coins on focus
- **Currency Selector** — Switch between 10 currencies (USD, EUR, GBP, JPY, AUD, CAD, CHF, CNY, KRW, INR)
- **Trending Coins** — Horizontally scrollable banner of trending cryptocurrencies on the homepage
- **Responsive Design** — Table layout on desktop, card layout on mobile, adaptive charts
- **Skeleton Loading** — Animated placeholders while data loads (no content jumping)
- **SSR** — Server-side rendered pages for fast initial load and SEO
- **SEO** — Dynamic meta tags (title, description, Open Graph) on all pages

## Architecture

```
Browser ──> Nuxt SSR Server ──> Laravel API ──> CoinGecko API
                │                    │
                │                    └── Redis Cache
                │
                └── Server Routes (proxy layer)
```

The frontend never talks to Laravel directly. All API calls go through Nuxt server routes (`/server/api/*`), which proxy to the Laravel backend. This means:
- The Laravel backend URL is never exposed to the browser
- No CORS configuration needed (same-origin requests)
- SSR data fetching works without conditional URL logic

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running

## Getting Started

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd CoinGecko
   ```

2. **Start the application**

   ```bash
   docker compose up
   ```

   First run takes a few minutes to pull images and install dependencies. The `.env` file is automatically created from `.env.example` on first boot.

   > **Note:** For this submission, a working CoinGecko demo API key is included so the project runs immediately. In production, secrets would be injected via a secrets manager (e.g. AWS Secrets Manager) or CI/CD pipeline variables.

3. **Open in your browser**

   - **Frontend:** [http://localhost:3000](http://localhost:3000)
   - **Backend API:** [http://localhost:8000](http://localhost:8000)

## Usage

- **Browse** — The homepage shows the top 10 cryptocurrencies with a trending banner above
- **Sort** — Click any column header in the table to sort ascending/descending
- **View Details** — Click a coin row to see full details, stats, price chart, and exchange tickers
- **Search** — Type in the search bar (min 2 characters) to find any cryptocurrency by name or symbol
- **Change Currency** — Use the dropdown next to the search bar to switch display currency
- **Chart Periods** — On a detail page, toggle between 24H, 7D, 30D, 90D, and 1Y price history

## Project Structure

### Backend (Laravel — DDD)

The backend follows a Domain-Driven Design pattern. All crypto-related code lives under `app/Domains/Crypto/`, keeping it isolated from framework boilerplate. To add a new domain (e.g. user portfolios), you'd create `app/Domains/Portfolio/` with the same `Http/Controllers`, `Http/Requests`, `Services` structure. No database is needed — `CoinGeckoService` acts as a cached HTTP client, registered as a singleton via `AppServiceProvider`. All user input is validated through dedicated `FormRequest` classes before reaching the controller.

```
backend/
├── app/
│   ├── Domains/Crypto/
│   │   ├── Http/
│   │   │   ├── Controllers/              # CryptocurrencyController
│   │   │   └── Requests/                 # SearchCryptoRequest, MarketChartRequest
│   │   └── Services/                     # CoinGeckoService (cached via Redis)
│   └── Providers/                        # AppServiceProvider (singleton bindings)
├── config/coingecko.php                  # API key, base URL, timeout
├── routes/api.php                        # 5 endpoints, throttle middleware
└── tests/                                # 31 unit + feature tests
```

### Frontend (Nuxt 3)

Components are split into two directories: `ui/` contains generic, reusable pieces (`Card`, `StatItem`, `ErrorMessage`) that have no knowledge of crypto data and can be used anywhere. `crypto/` contains domain-specific components (`CryptoTable`, `PriceChart`) that depend on the API data shape. This separation means `Card` is defined once and used by the detail page, chart, and tickers — not duplicated across views.

Composables follow the same pattern: `useApi` handles data fetching via server routes, `useCurrency` manages shared currency state across all components using Nuxt's `useState` (SSR-safe), and `useFormatters` centralises all number/currency display logic so formatting rules are consistent everywhere.

Server routes under `server/api/` act as the SSR proxy layer. They forward requests to the Laravel backend and return clean error messages, keeping the backend URL out of client bundles entirely.

Pages use Nuxt's file-based routing — `pages/index.vue` is the homepage, `pages/crypto/[id].vue` handles dynamic coin detail routes.

```
frontend/
├── app/
│   ├── components/
│   │   ├── crypto/                       # CryptoDetail, CryptoTable, PriceChart,
│   │   │                                 #   TrendingCoins, TradingTickers
│   │   └── ui/                           # Card, StatItem, SearchBar, CurrencySelector,
│   │                                     #   ErrorMessage, LoadingSpinner, SortIcon
│   ├── composables/                      # useApi, useCurrency, useFormatters
│   ├── layouts/                          # Default layout (header, search, footer)
│   └── pages/                            # index, crypto/[id]
├── server/
│   └── api/cryptocurrencies/             # SSR proxy routes to Laravel
└── tests/                                # 33 composable + component tests
```

## API Flow

All frontend requests go through Nuxt server routes, which proxy to the Laravel backend:

| Frontend Path | Server Route | Laravel Endpoint |
|---|---|---|
| `/api/cryptocurrencies` | `server/api/cryptocurrencies/index.get.js` | `GET /api/cryptocurrencies` |
| `/api/cryptocurrencies/trending` | `server/api/cryptocurrencies/trending.get.js` | `GET /api/cryptocurrencies/trending` |
| `/api/cryptocurrencies/search?q=` | `server/api/cryptocurrencies/search.get.js` | `GET /api/cryptocurrencies/search` |
| `/api/cryptocurrencies/:id` | `server/api/cryptocurrencies/[id].get.js` | `GET /api/cryptocurrencies/:id` |
| `/api/cryptocurrencies/:id/market-chart` | `server/api/cryptocurrencies/[id]/market-chart.get.js` | `GET /api/cryptocurrencies/:id/market-chart` |

All Laravel endpoints return a consistent JSON format and are rate-limited to 100 requests per minute:
```json
{ "success": true, "data": { ... } }
```

## Caching

Redis caches all CoinGecko API responses with TTLs based on data volatility:

| Data | TTL | Reason |
|------|-----|--------|
| Market prices / coin detail | 60s | Price-sensitive, needs freshness |
| Search results | 5 min | Stable data, rarely changes |
| Trending coins | 5 min | Updates infrequently |
| Price chart history | 5 min | Historical data, no urgency |

## Security

- CoinGecko API key stored server-side only (Laravel `.env`), never in client bundles
- All API requests proxied through Nuxt server routes (backend URL hidden)
- Input validation via Laravel FormRequest classes
- Rate limiting on all API routes (100 req/min)
- Route validation on detail page (rejects invalid coin IDs)

## Testing

```bash
# Backend (31 tests, 66 assertions)
docker compose exec backend php artisan test

# Frontend (33 tests)
docker compose exec frontend npm test
```

Backend tests cover all service methods, API endpoints, validation rules, and error handling. Frontend tests cover composable logic (formatters, currency state) and UI component rendering.

## Future Improvements

Given more time, the following areas would strengthen the application:

- **WebSocket integration** — Replace 60-second polling with real-time price updates for a truly live dashboard
- **User authentication** — Allow users to create accounts with personal watchlists and favourite coins
- **Price alerts** — Notify users when a coin hits a target price via email or push notifications
- **E2E testing** — Add Playwright tests covering full user flows (search, navigate, change currency)
- **Production builds** — Multi-stage Dockerfiles with Nginx for optimised static serving and proper health checks
- **CI/CD pipeline** — Automated testing, linting, and deployment on push via GitHub Actions
- **Pagination** — Extend the homepage beyond top 10 with infinite scroll or paginated results

## Stopping the Application

```bash
docker compose down
```
