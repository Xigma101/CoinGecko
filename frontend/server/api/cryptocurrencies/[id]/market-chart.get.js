export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  const id = getRouterParam(event, 'id')
  const query = getQuery(event)

  try {
    return await $fetch(`${config.apiBaseUrl}/api/cryptocurrencies/${id}/market-chart`, { query })
  } catch (error) {
    throw createError({
      statusCode: error.statusCode || 502,
      statusMessage: 'Unable to fetch chart data',
    })
  }
})
