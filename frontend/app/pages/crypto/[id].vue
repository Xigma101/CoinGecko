<template>
  <div>
    <NuxtLink to="/" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted hover:text-white transition-colors mb-6">
      <ArrowLeftIcon class="w-4 h-4" />
      Back to top coins
    </NuxtLink>

    <!-- Skeleton while loading -->
    <template v-if="status === 'pending'">
      <!-- Header skeleton -->
      <div class="flex items-center gap-4 mb-8">
        <div class="w-16 h-16 bg-dark-border rounded-full animate-pulse" />
        <div class="space-y-2">
          <div class="h-7 w-40 bg-dark-border rounded-full animate-pulse" />
          <div class="h-4 w-24 bg-dark-border rounded-full animate-pulse" />
        </div>
      </div>
      <!-- Price + stats skeleton -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <Card class="lg:col-span-1">
          <div class="h-4 w-20 bg-dark-border rounded-full animate-pulse mb-2" />
          <div class="h-8 w-36 bg-dark-border rounded-full animate-pulse mb-2" />
          <div class="h-4 w-16 bg-dark-border rounded-full animate-pulse" />
        </Card>
        <Card class="lg:col-span-2">
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div v-for="i in 6" :key="i" class="space-y-2">
              <div class="h-3 w-16 bg-dark-border rounded-full animate-pulse" />
              <div class="h-5 w-24 bg-dark-border rounded-full animate-pulse" />
            </div>
          </div>
        </Card>
      </div>
      <!-- Chart skeleton -->
      <Card class="mb-8">
        <div class="h-5 w-28 bg-dark-border rounded-full animate-pulse mb-4" />
        <div class="h-[200px] sm:h-[300px] bg-dark-border/30 rounded animate-pulse" />
      </Card>
    </template>

    <!-- Error state -->
    <div v-else-if="error" class="mt-4">
      <ErrorMessage :message="error.message || 'Failed to load cryptocurrency data'" @retry="refresh" />
    </div>

    <!-- Content -->
    <template v-else-if="data?.data">
      <CryptoDetail :coin="data.data">
        <PriceChart :coin-id="id" />
      </CryptoDetail>

      <div class="mt-8">
        <TradingTickers
          :tickers="data.data.tickers || []"
          :last-updated="lastUpdatedText"
        />
      </div>
    </template>
  </div>
</template>

<script setup>
import { ArrowLeftIcon } from '@heroicons/vue/20/solid'

definePageMeta({
  validate: async (route) => /^[a-z0-9-]+$/.test(route.params.id),
})

const route = useRoute()
const id = route.params.id

const { getCoinDetail } = useApi()
const { data, error, status, refresh } = await getCoinDetail(id)

const coinName = computed(() => data.value?.data?.name ?? 'Cryptocurrency')
const coinSymbol = computed(() => data.value?.data?.symbol?.toUpperCase() ?? '')

useHead({
  title: () => `${coinName.value} (${coinSymbol.value}) — CryptoTracker`,
})

useSeoMeta({
  description: () => `${coinName.value} (${coinSymbol.value}) live price, market cap, volume, supply, and price history chart. Powered by CoinGecko.`,
  ogTitle: () => `${coinName.value} (${coinSymbol.value}) — CryptoTracker`,
  ogDescription: () => `Track ${coinName.value} price, market data, and trading activity across exchanges.`,
  ogType: 'website',
  ogImage: () => data.value?.data?.image?.large ?? '',
})

const now = ref(Date.now())
let pollInterval
let tickInterval

onMounted(async () => {
  await nextTick()
  refresh()
  pollInterval = setInterval(() => refresh(), 60000)
  tickInterval = setInterval(() => { now.value = Date.now() }, 1000)
})
onUnmounted(() => {
  clearInterval(pollInterval)
  clearInterval(tickInterval)
})

const lastUpdatedText = computed(() => {
  const ts = data.value?.data?.last_updated
  if (!ts) return ''
  const diff = Math.round((now.value - new Date(ts).getTime()) / 1000)
  if (diff < 60) return `${diff}s ago`
  if (diff < 3600) return `${Math.round(diff / 60)}m ago`
  return `${Math.round(diff / 3600)}h ago`
})
</script>
