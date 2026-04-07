<template>
  <div class="bg-dark-light rounded-lg border border-dark-border p-4 sm:p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-white">Markets</h2>
      <span v-if="lastUpdated" class="text-xs text-muted">
        Updated {{ lastUpdated }}
      </span>
    </div>

    <div v-if="!tickers?.length" class="text-center py-6">
      <p class="text-sm text-muted">No market data available</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-muted uppercase border-b border-dark-border">
          <tr>
            <th class="px-4 py-2">Exchange</th>
            <th class="px-4 py-2">Pair</th>
            <th class="px-4 py-2 text-right">Price</th>
            <th class="px-4 py-2 text-right hidden sm:table-cell">Volume (24h)</th>
            <th class="px-4 py-2 text-right hidden md:table-cell">Spread</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(ticker, i) in visibleTickers"
            :key="i"
            class="border-b border-dark-border/50 hover:bg-dark-lighter transition-colors"
          >
            <td class="px-4 py-3 text-white font-medium">{{ ticker.market?.name }}</td>
            <td class="px-4 py-3">
              <span class="text-white">{{ ticker.base }}</span>
              <span class="text-muted">/{{ ticker.target }}</span>
            </td>
            <td class="px-4 py-3 text-right text-white">${{ formatTickerPrice(ticker.last) }}</td>
            <td class="px-4 py-3 text-right text-gray-300 hidden sm:table-cell">{{ formatTickerVolume(ticker.volume) }}</td>
            <td class="px-4 py-3 text-right hidden md:table-cell">
              <span
                class="text-xs font-medium px-2 py-0.5 rounded-full"
                :class="ticker.bid_ask_spread_percentage < 0.5 ? 'text-green-400 bg-green-400/10' : ticker.bid_ask_spread_percentage < 2 ? 'text-yellow-400 bg-yellow-400/10' : 'text-red-400 bg-red-400/10'"
              >
                {{ ticker.bid_ask_spread_percentage?.toFixed(2) ?? '—' }}%
              </span>
            </td>
          </tr>
        </tbody>
      </table>

      <button
        v-if="tickers.length > 10 && !showAll"
        class="w-full mt-3 py-2 text-sm text-accent hover:text-accent-hover transition-colors"
        @click="showAll = true"
      >
        Show all {{ tickers.length }} markets
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  tickers: { type: Array, default: () => [] },
  lastUpdated: { type: String, default: '' },
})

const showAll = ref(false)

const visibleTickers = computed(() => {
  if (showAll.value) return props.tickers
  return props.tickers.slice(0, 10)
})

function formatTickerPrice(price) {
  if (price == null) return '—'
  const abs = Math.abs(price)
  if (abs > 0 && abs < 0.0000001) return price.toFixed(10)
  if (abs < 0.00001) return price.toFixed(8)
  if (abs < 0.01) return price.toFixed(5)
  if (abs < 1) return price.toFixed(4)
  return price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function formatTickerVolume(vol) {
  if (vol == null) return '—'
  if (vol >= 1e9) return `$${(vol / 1e9).toFixed(2)}B`
  if (vol >= 1e6) return `$${(vol / 1e6).toFixed(2)}M`
  if (vol >= 1e3) return `$${(vol / 1e3).toFixed(1)}K`
  return `$${vol.toFixed(2)}`
}
</script>
