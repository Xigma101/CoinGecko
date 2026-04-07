# Frontend Plan (Nuxt 3 + Tailwind)

> Nuxt 3 app with file-based routing, Tailwind CSS, and a clean responsive UI for browsing crypto data.

---

## Project Setup

- [ ] Scaffold Nuxt 3 project (`npx nuxi init frontend`)
- [ ] Install Tailwind CSS via `@nuxtjs/tailwindcss` module
- [ ] Configure `nuxt.config.ts` runtime config for backend API base URL
- [ ] Create API composable (`composables/useApi.ts`) for making requests to Laravel backend

## Layouts

- [ ] `layouts/default.vue` -- app shell
  - Header with app title/logo
  - Search bar integrated in header
  - Main content slot
  - Footer with attribution
  - Responsive: hamburger menu on mobile (if nav items needed)

## Pages (File-Based Routing)

- [ ] `pages/index.vue` -- Homepage
  - Fetches top 10 cryptocurrencies on load
  - Displays via `CryptoTable` component
  - Loading skeleton while fetching
  - Error state with retry button
- [ ] `pages/crypto/[id].vue` -- Detail Page
  - Fetches coin detail by route param `id`
  - Displays via `CryptoDetail` component
  - Back navigation to homepage
  - Loading and error states

## Components

- [ ] `CryptoTable.vue`
  - Desktop: table with columns (Rank, Name, Symbol, Price, 24h %, Market Cap)
  - Mobile: card/list layout with key info
  - Rows are clickable, navigate to `/crypto/{id}`
- [ ] `CryptoCard.vue`
  - Coin logo, name, symbol
  - Current price, 24h change (color coded)
  - Market cap
  - Used as mobile view in table and in search results
- [ ] `SearchBar.vue`
  - Text input with debounced API calls (300ms)
  - Dropdown results list below input
  - Searches by name and symbol
  - Click result navigates to `/crypto/{id}`
  - Close dropdown on blur/escape
- [ ] `CryptoDetail.vue`
  - Coin logo, name, symbol, rank
  - Current price (large)
  - 24h price change (color coded)
  - Market cap, 24h volume, circulating supply
  - Description (from CoinGecko, HTML sanitized)
- [ ] `LoadingSpinner.vue` -- reusable spinner/skeleton
- [ ] `ErrorMessage.vue` -- reusable error display with retry action

## Responsive Design (Tailwind)

- [ ] Mobile-first approach throughout
- [ ] Homepage: card stack on mobile, data table on `md:` and above
- [ ] Detail page: single column on mobile, two-column on `lg:`
- [ ] Search dropdown: full-width on mobile, max-width on desktop
- [ ] Consistent spacing scale, readable typography
- [ ] Dark/neutral color scheme (crypto-appropriate)

## UX Polish

- [ ] Number formatting:
  - Currency values with `$` and appropriate decimal places
  - Large numbers abbreviated (e.g., $1.2T, $45.3B)
  - Percentage with +/- sign
- [ ] Color coding: green for positive change, red for negative
- [ ] Coin logos from CoinGecko `image` field
- [ ] Page transitions (Nuxt `<NuxtPage>` transition)
- [ ] SEO meta tags via `useHead()` on each page
- [ ] Favicon
