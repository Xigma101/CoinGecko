# Deployment & Setup Plan

> **Priority: HIGH** -- Get the app servable locally before building features.

## Architecture

- **No database** -- Laravel acts purely as an API proxy to CoinGecko
- **Two containers** -- Laravel backend + Nuxt frontend via Docker Compose
- **One command** -- `docker-compose up` launches everything

---

## Docker Setup

- [x] Create `docker-compose.yml` at project root with two services:
  - `backend` (Laravel/PHP)
  - `frontend` (Nuxt/Node)
- [x] Create `backend/Dockerfile`
  - PHP 8.4 with required extensions
  - Composer installed
  - Serve via `php artisan serve --host=0.0.0.0 --port=8000`
- [x] Create `frontend/Dockerfile`
  - Node 20+ (Alpine for size)
  - Install dependencies, run Nuxt dev server
  - Serve on port 3000
- [x] Add `.dockerignore` to both projects

## Environment & Security

- [x] Create `.env.example` in `backend/` with:
  - `COINGECKO_API_KEY=your_key_here`
  - `COINGECKO_BASE_URL=https://api.coingecko.com/api/v3`
- [x] Ensure `.env` is in `.gitignore`
- [x] API key is only used server-side (never exposed to frontend)

## Networking

- [x] Backend exposed on `localhost:8000`
- [x] Frontend exposed on `localhost:3000`
- [x] Frontend proxy config: API calls to `/api/*` route to backend container
- [x] CORS configured on Laravel to accept requests from frontend origin

## Setup Instructions (for README)

- [x] Clone repo
- [x] Copy `.env.example` to `.env` and add CoinGecko API key
- [x] Run `docker-compose up`
- [x] Visit `http://localhost:3000`

## Verification

- [x] `docker-compose up` builds and starts both services without errors
- [x] Frontend loads at `localhost:3000` (HTTP 200)
- [x] Frontend can reach backend API at `localhost:8000/api/...`
- [x] Restart (`docker-compose down && docker-compose up`) works cleanly
