<template>
  <div>
    <NuxtLink to="/" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted hover:text-white transition-colors mb-6">
      <ArrowLeftIcon class="w-4 h-4" />
      Back to top coins
    </NuxtLink>

    <div v-if="status === 'pending'" class="flex justify-center py-12">
      <LoadingSpinner />
    </div>

    <div v-else-if="error" class="text-center py-12">
      <ErrorMessage :message="error.message" @retry="refresh" />
    </div>

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

const route = useRoute()
const id = route.params.id

const { getCoinDetail } = useApi()
const { data, error, status, refresh } = await getCoinDetail(id)

useHead({
  title: data.value?.data?.name
    ? `${data.value.data.name} — CryptoTracker`
    : 'CryptoTracker',
})

// Auto-refresh every 60 seconds (matches CoinGecko cache)
// Tick counter updates the "last updated" text every second
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
