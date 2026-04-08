export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()

  try {
    return await $fetch(`${config.apiBaseUrl}/api/cryptocurrencies/trending`)
  } catch (error) {
    throw createError({
      statusCode: error.statusCode || 502,
      statusMessage: 'Unable to fetch trending data',
    })
  }
})
