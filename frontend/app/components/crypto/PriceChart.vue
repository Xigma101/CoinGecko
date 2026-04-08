<template>
  <Card>
    <!-- Header with title and time period buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
      <h2 class="text-lg font-semibold text-white">Price History</h2>
      <div class="flex gap-1">
        <button
          v-for="period in periods"
          :key="period.days"
          class="px-2.5 sm:px-3 py-1 text-xs sm:text-sm font-medium rounded-lg transition-colors"
          :class="
            selectedDays === period.days
              ? 'bg-accent text-dark font-semibold'
              : 'text-muted hover:bg-dark-lighter'
          "
          @click="selectedDays = period.days"
        >
          {{ period.label }}
        </button>
      </div>
    </div>

    <!-- Loading state -->
    <!-- Loading skeleton matching chart dimensions -->
    <div v-if="status === 'pending'" class="h-[200px] sm:h-[300px] bg-dark-border/20 rounded animate-pulse" />

    <!-- Error / unavailable -->
    <div v-else-if="error || (status !== 'pending' && !chartData.length)" class="py-4">
      <ErrorMessage message="Chart data unavailable" inline @retry="refresh" />
    </div>

    <!-- Chart -->
    <div v-else-if="chartData.length">
      <LineChart
        :data="chartData"
        :height="isMobile ? 200 : 300"
        :categories="chartCategories"
        :x-formatter="formatXAxis"
        :y-formatter="formatYAxis"
        curve-type="monotoneX"
        :x-num-ticks="isMobile ? 3 : 6"
        :y-num-ticks="isMobile ? 3 : 5"
        :hide-legend="true"
        :line-width="2"
      />
    </div>
  </Card>
</template>

<script setup>
const props = defineProps({
  coinId: { type: String, required: true },
})

const { currency, currentCurrency } = useCurrency()
const { getMarketChart } = useApi()

// Responsive breakpoint (VueUse — SSR-safe, auto-cleanup)
const { width } = useWindowSize()
const isMobile = computed(() => width.value < 640)

const periods = [
  { label: '24H', days: '1' },
  { label: '7D', days: '7' },
  { label: '30D', days: '30' },
  { label: '90D', days: '90' },
  { label: '1Y', days: '365' },
]
const selectedDays = ref('7')

const { data, error, status, refresh } = getMarketChart(
  props.coinId,
  currency,
  selectedDays
)

const chartData = computed(() => {
  const prices = data.value?.data?.prices
  if (!prices?.length) return []

  return prices.map(([timestamp, price]) => {
    const d = new Date(timestamp)
    const date = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    const time = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
    return {
      date: `${date}, ${time}`,
      price,
    }
  })
})

const chartCategories = {
  price: { name: 'Price', color: '#f0b90b' },
}

function formatXAxis(tick) {
  const item = chartData.value[tick]
  if (!item) return ''

  // On mobile, show shorter labels
  if (isMobile.value) {
    const d = new Date(chartData.value[tick]?.date)
    if (!isNaN(d)) return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    // Fallback: trim the time portion
    return item.date.split(',')[0]
  }

  return item.date
}

function formatYAxis(tick) {
  const sym = currentCurrency.value?.symbol ?? '$'
  const abs = Math.abs(tick)

  // On mobile, always abbreviate
  if (isMobile.value) {
    if (abs >= 1e6) return `${sym}${(tick / 1e6).toFixed(1)}M`
    if (abs >= 1000) return `${sym}${(tick / 1000).toFixed(1)}k`
    if (abs < 0.01) return `${sym}${tick.toFixed(4)}`
    return `${sym}${tick.toFixed(2)}`
  }

  if (abs >= 1e6) return `${sym}${(tick / 1e6).toFixed(2)}M`
  if (abs >= 1000) return `${sym}${tick.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
  if (abs > 0 && abs < 0.0000001) return `${sym}${tick.toFixed(10)}`
  if (abs < 0.00001) return `${sym}${tick.toFixed(8)}`
  if (abs < 0.01) return `${sym}${tick.toFixed(5)}`
  if (abs < 1) return `${sym}${tick.toFixed(4)}`
  return `${sym}${tick.toFixed(2)}`
}
</script>
