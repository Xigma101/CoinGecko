<template>
  <div class="mb-10">
    <div class="flex items-center gap-2 mb-4">
      <FireIcon class="w-5 h-5 text-orange-500" />
      <h2 class="text-lg font-semibold text-white">Trending</h2>
    </div>

    <!-- Always reserve height with a fixed container -->
    <div class="flex gap-3 overflow-x-auto scrollbar-hide -mx-1 px-1 pt-0 pb-4 mt-2">
      <!-- Skeleton cards while loading -->
      <template v-if="status === 'pending' || (!coins.length && !error)">
        <div v-for="i in 5" :key="i" class="flex-shrink-0 w-52 bg-dark-light rounded-lg border border-dark-border p-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 bg-dark-border rounded-full animate-pulse" />
            <div class="space-y-1">
              <div class="h-3 w-16 bg-dark-border rounded-full animate-pulse" />
              <div class="h-2 w-10 bg-dark-border rounded-full animate-pulse" />
            </div>
          </div>
          <div class="h-4 w-20 bg-dark-border rounded-full animate-pulse" />
        </div>
      </template>

      <!-- Trending cards -->
      <template v-else-if="coins.length">
        <NuxtLink
          v-for="coin in coins"
          :key="coin.item.id"
          :to="`/crypto/${coin.item.id}`"
          class="flex-shrink-0 w-52 bg-dark-light rounded-lg border border-dark-border p-4 hover:border-accent/30 transition-all"
        >
          <div class="flex items-center gap-2 mb-2">
            <NuxtImg :src="coin.item.thumb" :alt="coin.item.name" width="32" height="32" class="w-8 h-8 rounded-full" />
            <div class="min-w-0">
              <p class="text-sm font-medium text-white truncate">{{ coin.item.name }}</p>
              <p class="text-xs text-muted uppercase">{{ coin.item.symbol }}</p>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <span v-if="coin.item.data?.price" class="text-sm font-semibold text-white truncate">
              {{ formatTrendingPrice(coin.item.data.price) }}
            </span>
            <span
              v-if="coin.item.data?.price_change_percentage_24h?.usd != null"
              class="text-xs font-medium"
              :class="coin.item.data.price_change_percentage_24h.usd >= 0 ? 'text-green-500' : 'text-red-500'"
            >
              {{ formatPercentage(coin.item.data.price_change_percentage_24h.usd) }}
            </span>
          </div>
        </NuxtLink>
      </template>
    </div>

    <!-- Inline error below the container -->
    <ErrorMessage v-if="error" message="Trending data unavailable" inline @retry="refresh" />
  </div>
</template>

<script setup>
import { FireIcon } from '@heroicons/vue/20/solid'

const { getTrending } = useApi()
const { data, error, status, refresh } = getTrending()
const { formatCurrency, formatPercentage } = useFormatters()

const coins = computed(() => data.value?.data?.slice(0, 7) ?? [])

function formatTrendingPrice(rawPrice) {
  if (!rawPrice) return '—'
  const num = parseFloat(String(rawPrice).replace(/[^0-9.\-]/g, ''))
  if (isNaN(num)) return rawPrice
  return formatCurrency(num)
}
</script>
