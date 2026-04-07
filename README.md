# CoinGecko Crypto Dashboard

A web application that displays the top cryptocurrencies by market cap, with search and detail views. Built with Laravel (backend API) and Nuxt 3 (frontend), styled with Tailwind CSS.

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.4)
- **Frontend:** Nuxt 3 (Vue.js) with Tailwind CSS
- **API:** CoinGecko REST API
- **Infrastructure:** Docker Compose

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

   > **Note:** For this interview submission, a working CoinGecko API key is included directly in `.env.example` so the project can be run immediately without any additional configuration. In a production environment, `.env.example` would contain placeholder values only, and real secrets would be injected during deployment via a secrets manager (e.g. AWS Secrets Manager) or CI/CD pipeline variables.

3. **Start the application**

   ```bash
   docker compose up
   ```

   This builds and starts both services. First run will take a few minutes to pull images and install dependencies.

4. **Open in your browser**

   - **Frontend:** [http://localhost:3000](http://localhost:3000)
   - **Backend API:** [http://localhost:8000](http://localhost:8000)

## Project Structure

```
CoinGecko/
├── backend/           # Laravel API (proxies requests to CoinGecko)
├── frontend/          # Nuxt 3 frontend
├── project_plans/     # Implementation planning documents
├── docker-compose.yml
└── README.md
```

## Stopping the Application

```bash
docker compose down
```
