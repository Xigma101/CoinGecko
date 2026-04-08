# Frontend Plan (Nuxt 3 + Tailwind)

> Nuxt 3 app with file-based routing, Tailwind CSS, SSR, and a responsive dark-themed UI.

---

## Project Setup

- [x] Scaffold Nuxt 3 project
- [x] Install and configure Tailwind CSS (`@nuxtjs/tailwindcss` module)
- [x] Install and configure `nuxt-charts` module for price history
- [x] Install `@heroicons/vue` for icons
- [x] Configure runtime config for backend API base URL (SSR-aware)
- [x] Configure Vite polling for Docker volume file watching
- [x] Custom dark theme via Tailwind config
- [x] Global CSS for dark scrollbars and scrollbar-hide utility
- [x] Converted to plain JavaScript (removed TypeScript)

## Composables

- [x] `useApi.js` — SSR-aware API client (`import.meta.server` for Docker internal URL)
  - `getTopCoins(currency)` — with reactive watch
  - `getCoinDetail(id)`
  - `searchCoins(query)`
  - `getMarketChart(id, currency, days)` — lazy fetch
  - `getTrending()` — lazy fetch
- [x] `useCurrency.js` — shared reactive currency state via `useState`
  - 10 supported currencies (USD, EUR, GBP, JPY, AUD, CAD, CHF, CNY, KRW, INR)
- [x] `useFormatters.js` — currency-aware formatting
  - Dynamic decimal precision (2/4/5/8/10 based on value magnitude)
  - Large number abbreviation (T/B/M)
  - Percentage with +/- sign

## Layouts

- [x] `layouts/default.vue` — dark-themed app shell
  - Sticky header with logo, search bar, currency selector
  - Main content slot
  - Footer with CoinGecko attribution

## Pages (File-Based Routing)

- [x] `pages/index.vue` — Homepage
  - Trending coins banner
  - Top 10 cryptocurrency table
  - Loading skeleton states
  - Error state with retry
- [x] `pages/crypto/[id].vue` — Detail Page
  - Coin header (image, name, rank)
  - Price + stats grid via reusable components
  - Price history chart (slotted into detail)
  - Trading tickers table
  - Auto-refresh polling (60s) with live "Updated Xs ago"
  - Back navigation

## Components — UI (Generic, Reusable)

- [x] `ui/Card.vue` — base card container (used by detail, chart, tickers)
- [x] `ui/StatItem.vue` — label + value pair (used in stats grid)
- [x] `ui/SearchBar.vue` — debounced search with preloaded trending on focus
- [x] `ui/CurrencySelector.vue` — dropdown with 10 currencies
- [x] `ui/LoadingSpinner.vue` — animated spinner
- [x] `ui/ErrorMessage.vue` — error display with retry button
- [x] `ui/SortIcon.vue` — sort direction indicator (heroicons)

## Components — Crypto (Domain-Specific)

- [x] `crypto/CryptoTable.vue` — sortable table (5 columns) + mobile card layout
  - Skeleton loading rows
  - Sort by rank, price, 24h change, market cap, volume
- [x] `crypto/CryptoDetail.vue` — coin detail with slot for chart
  - Uses `Card` and `StatItem` for DRY display
- [x] `crypto/PriceChart.vue` — line chart with time period toggle
  - Responsive: fewer ticks and shorter height on mobile
  - Adaptive formatters for micro-cap coins
- [x] `crypto/TrendingCoins.vue` — horizontal scrollable banner
- [x] `crypto/TradingTickers.vue` — exchange pairs table with spread indicators

## SSR & SEO

- [x] Server-side rendering via Nuxt (data fetched on server, full HTML sent to browser)
- [x] `useHead()` on all pages for dynamic titles
- [x] `useSeoMeta()` on all pages for description, og:title, og:description, og:image
- [x] SSR-aware API calls (Docker internal URL on server, localhost on client)

## Responsive Design (Tailwind)

- [x] Mobile-first approach
- [x] Homepage: card stack on mobile, data table on desktop
- [x] Detail page: single column on mobile, grid on desktop
- [x] Chart: 200px height + 3 ticks on mobile, 300px + 6 ticks on desktop
- [x] Search dropdown: full-width on mobile
- [x] Trending banner: horizontal scroll with hidden scrollbar
- [x] Tickers: volume and spread columns hidden on mobile

## Test Coverage

- [x] Vitest + @vue/test-utils + happy-dom
- [x] Composable tests: useFormatters (17 tests), useCurrency (4 tests)
- [x] Component tests: Card, StatItem, ErrorMessage, SortIcon (11 tests)
- [x] 32 tests passing
