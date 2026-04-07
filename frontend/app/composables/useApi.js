/**
 * Composable for making requests to the Laravel backend API.
 *
 * Wraps useFetch with the correct base URL and consistent
 * error handling for the {success, data, message} response format.
 */
export function useApi() {
  const config = useRuntimeConfig()
  const baseUrl = import.meta.server
    ? config.apiBaseUrl
    : config.public.apiBaseUrl

  /**
   * Fetch the top 10 cryptocurrencies by market cap.
   * Re-fetches automatically when currency changes.
   */
  function getTopCoins(currency) {
    return useFetch(`${baseUrl}/api/cryptocurrencies`, {
      params: { currency },
      watch: [currency],
    })
  }

  /**
   * Fetch detailed info for a specific cryptocurrency.
   */
  function getCoinDetail(id) {
    return useFetch(`${baseUrl}/api/cryptocurrencies/${id}`)
  }

  /**
   * Search cryptocurrencies by name or symbol.
   */
  function searchCoins(query) {
    return useFetch(`${baseUrl}/api/cryptocurrencies/search`, {
      params: { q: query },
    })
  }

  /**
   * Fetch historical price chart data for a cryptocurrency.
   * Re-fetches when currency or days change.
   */
  function getMarketChart(id, currency, days) {
    return useLazyFetch(`${baseUrl}/api/cryptocurrencies/${id}/market-chart`, {
      params: { currency, days },
      watch: [currency, days],
    })
  }

  /**
   * Fetch trending cryptocurrencies.
   */
  function getTrending() {
    return useLazyFetch(`${baseUrl}/api/cryptocurrencies/trending`)
  }

  return { getTopCoins, getCoinDetail, searchCoins, getMarketChart, getTrending }
}
