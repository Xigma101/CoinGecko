export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  const id = getRouterParam(event, 'id')

  try {
    return await $fetch(`${config.apiBaseUrl}/api/cryptocurrencies/${id}`)
  } catch (error) {
    throw createError({
      statusCode: error.statusCode || 502,
      statusMessage: 'Unable to fetch cryptocurrency details',
    })
  }
})
