/**
 * Composable for making requests to the backend API via Nuxt server routes.
 *
 * All requests go through /server/api/* which proxies to Laravel,
 * keeping the backend URL hidden and eliminating CORS concerns.
 */
export function useApi() {
  /**
   * Fetch the top 10 cryptocurrencies by market cap.
   * Re-fetches automatically when currency changes.
   */
  function getTopCoins(currency) {
    return useFetch('/api/cryptocurrencies', {
      params: { currency },
      watch: [currency],
    })
  }

  /**
   * Fetch detailed info for a specific cryptocurrency.
   */
  function getCoinDetail(id) {
    return useFetch(`/api/cryptocurrencies/${id}`)
  }

  /**
   * Search cryptocurrencies by name or symbol.
   */
  function searchCoins(query) {
    return useFetch('/api/cryptocurrencies/search', {
      params: { q: query },
    })
  }

  /**
   * Fetch historical price chart data for a cryptocurrency.
   * Re-fetches when currency or days change.
   */
  function getMarketChart(id, currency, days) {
    return useLazyFetch(`/api/cryptocurrencies/${id}/market-chart`, {
      params: { currency, days },
      watch: [currency, days],
    })
  }

  /**
   * Fetch trending cryptocurrencies.
   */
  function getTrending() {
    return useLazyFetch('/api/cryptocurrencies/trending')
  }

  return { getTopCoins, getCoinDetail, searchCoins, getMarketChart, getTrending }
}
