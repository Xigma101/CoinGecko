# CoinGecko Crypto Dashboard

A cryptocurrency tracking web application that displays real-time market data from the CoinGecko API. Users can browse the top 10 coins by market cap, view detailed information for any cryptocurrency, search by name or symbol, and compare prices across 10 different currencies.

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.4) — API proxy with DDD architecture
- **Frontend:** Nuxt 3 (Vue.js) with server-side rendering
- **Styling:** Tailwind CSS — responsive, mobile-first design
- **Charts:** nuxt-charts (Unovis) — interactive price history
- **API:** CoinGecko REST API
- **Infrastructure:** Docker Compose

## Features

- **Top 10 Cryptocurrencies** — Homepage displays the top coins ranked by market cap with price, 24h change, and volume
- **Sortable Table** — Click column headers to sort by rank, price, 24h change, market cap, or volume
- **Coin Detail Pages** — Click any coin for detailed stats including price, market cap, volume, supply, 24h high/low, and description
- **Price History Charts** — Interactive line charts with selectable time periods (24H, 7D, 30D, 90D, 1Y)
- **Search** — Debounced search bar with dropdown results, pre-loaded trending coins on focus
- **Currency Selector** — Switch between 10 currencies (USD, EUR, GBP, JPY, AUD, CAD, CHF, CNY, KRW, INR)
- **Trending Coins** — Horizontally scrollable banner of trending cryptocurrencies on the homepage
- **Responsive Design** — Table layout on desktop, card layout on mobile
- **Skeleton Loading** — Animated placeholders while data loads
- **SSR** — Server-side rendered pages for fast initial load

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
- **View Details** — Click a coin row to see full details, stats, and price chart
- **Search** — Type in the search bar (min 2 characters) to find any cryptocurrency by name or symbol
- **Change Currency** — Use the dropdown next to the search bar to switch display currency
- **Chart Periods** — On a detail page, toggle between 24H, 7D, 30D, 90D, and 1Y price history

## Project Structure

```
CoinGecko/
├── backend/                          # Laravel API
│   ├── app/
│   │   ├── Domains/Crypto/           # DDD domain layer
│   │   │   ├── Http/
│   │   │   │   ├── Controllers/      # CryptocurrencyController
│   │   │   │   └── Requests/         # FormRequest validation
│   │   │   └── Services/             # CoinGeckoService (API client)
│   │   └── Providers/
│   ├── config/coingecko.php          # API configuration
│   ├── routes/api.php                # API route definitions
│   └── tests/                        # Unit + Feature tests
├── frontend/                         # Nuxt 3 app
│   ├── app/
│   │   ├── components/
│   │   │   ├── crypto/               # Domain components (table, detail, chart, trending)
│   │   │   └── ui/                   # Reusable components (search, spinner, errors)
│   │   ├── composables/              # useApi, useCurrency, useFormatters
│   │   ├── layouts/                  # Default layout with header, search, footer
│   │   └── pages/                    # File-based routing (index, crypto/[id])
│   └── nuxt.config.ts
├── docker-compose.yml
└── README.md
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cryptocurrencies` | Top 10 coins by market cap (accepts `?currency=`) |
| GET | `/api/cryptocurrencies/trending` | Trending cryptocurrencies |
| GET | `/api/cryptocurrencies/search?q=` | Search by name or symbol |
| GET | `/api/cryptocurrencies/{id}` | Detailed coin information |
| GET | `/api/cryptocurrencies/{id}/market-chart` | Price history (accepts `?days=` and `?currency=`) |

All endpoints are rate-limited to 100 requests per minute and return a consistent JSON format:
```json
{ "success": true, "data": { ... } }
```

## Security

- CoinGecko API key is stored server-side only and never exposed to the frontend
- All API requests are proxied through the Laravel backend
- Input validation via Laravel FormRequest classes
- Rate limiting on all API routes
- CORS configured to restrict access to the frontend origin

## Testing

Run the backend test suite inside Docker:

```bash
docker compose exec backend php artisan test
```

31 tests covering all service methods, API endpoints, validation rules, and error handling.

## Stopping the Application

```bash
docker compose down
```
