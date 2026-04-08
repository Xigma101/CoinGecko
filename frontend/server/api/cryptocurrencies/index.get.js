export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  const query = getQuery(event)

  try {
    return await $fetch(`${config.apiBaseUrl}/api/cryptocurrencies`, { query })
  } catch (error) {
    throw createError({
      statusCode: error.statusCode || 502,
      statusMessage: 'Unable to fetch market data',
    })
  }
})
