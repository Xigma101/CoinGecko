<template>
  <div>
    <TrendingCoins />

    <h1 class="text-2xl font-bold text-white mb-6">Top 10 Cryptocurrencies</h1>

    <div v-if="error" class="text-center py-12">
      <ErrorMessage :message="error.message" @retry="refresh" />
    </div>

    <CryptoTable v-else :coins="data?.data" :loading="status === 'pending'" />
  </div>
</template>

<script setup>
const { currency } = useCurrency()
const { getTopCoins } = useApi()
const { data, error, status, refresh } = await getTopCoins(currency)

useHead({
  title: 'Top 10 Cryptocurrencies — CryptoTracker',
})
</script>
