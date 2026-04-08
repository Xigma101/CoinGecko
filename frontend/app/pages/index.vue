<template>
  <div>
    <TrendingCoins />

    <h1 class="text-2xl font-bold text-white mb-6">Top 10 Cryptocurrencies</h1>

    <CryptoTable :coins="data?.data" :loading="status === 'pending' || !!error" />
  </div>
</template>

<script setup>
definePageMeta({ keepalive: true })

const { currency } = useCurrency()
const { getTopCoins } = useApi()
const { data, error, status, refresh } = await getTopCoins(currency)

useHead({
  title: 'Top 10 Cryptocurrencies — CryptoTracker',
})

useSeoMeta({
  description: 'Track the top 10 cryptocurrencies by market cap. Live prices, 24h changes, volume, and trending coins powered by CoinGecko.',
  ogTitle: 'Top 10 Cryptocurrencies — CryptoTracker',
  ogDescription: 'Live cryptocurrency prices, market caps, and trends. Search any coin by name or symbol.',
  ogType: 'website',
})
</script>
