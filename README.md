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

2. **Set up the environment file**

   ```bash
   cp backend/.env.example backend/.env
   ```

   > **Note:** For this submission, a working CoinGecko demo API key is included in `.env.example` so the project runs immediately. In production, secrets would be injected via a secrets manager (e.g. AWS Secrets Manager) or CI/CD pipeline variables.

3. **Start the application**

   ```bash
   docker compose up
   ```

   First run takes a few minutes to pull images and install dependencies.

4. **Open in your browser**

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

```
CoinGecko/
├── backend/                              # Laravel API
│   ├── app/
│   │   ├── Domains/Crypto/               # DDD domain layer
│   │   │   ├── Http/
│   │   │   │   ├── Controllers/          # CryptocurrencyController
│   │   │   │   └── Requests/             # FormRequest validation
│   │   │   └── Services/                 # CoinGeckoService (cached API client)
│   │   └── Providers/
│   ├── config/coingecko.php
│   ├── routes/api.php
│   └── tests/                            # 31 unit + feature tests
│
├── frontend/                             # Nuxt 3 app
│   ├── app/
│   │   ├── components/
│   │   │   ├── crypto/                   # Domain: CryptoDetail, CryptoTable,
│   │   │   │                             #   PriceChart, TrendingCoins, TradingTickers
│   │   │   └── ui/                       # Reusable: Card, StatItem, SearchBar,
│   │   │                                 #   CurrencySelector, ErrorMessage, SortIcon
│   │   ├── composables/                  # useApi, useCurrency, useFormatters
│   │   ├── layouts/                      # Default layout (header, search, footer)
│   │   └── pages/                        # index, crypto/[id]
│   ├── server/
│   │   └── api/cryptocurrencies/         # SSR proxy routes to Laravel
│   └── tests/                            # 33 composable + component tests
│
├── docker-compose.yml                    # 3 services: backend, frontend, redis
└── README.md
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

## Stopping the Application

```bash
docker compose down
```
