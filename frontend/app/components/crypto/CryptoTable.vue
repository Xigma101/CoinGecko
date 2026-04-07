<template>
  <!-- Desktop: table layout -->
  <div class="hidden md:block overflow-x-auto rounded-lg border border-dark-border">
    <table class="w-full text-sm text-left">
      <thead class="bg-dark-light text-muted uppercase text-xs border-b border-dark-border">
        <tr>
          <th class="px-6 py-3 cursor-pointer select-none hover:text-white transition-colors" @click="toggleSort('market_cap_rank')">
            <span class="inline-flex items-center gap-1"># <SortIcon :active="sortKey === 'market_cap_rank'" :asc="sortAsc" /></span>
          </th>
          <th class="px-6 py-3">Coin</th>
          <th class="px-6 py-3 text-right cursor-pointer select-none hover:text-white transition-colors" @click="toggleSort('current_price')">
            <span class="inline-flex items-center gap-1 justify-end">Price <SortIcon :active="sortKey === 'current_price'" :asc="sortAsc" /></span>
          </th>
          <th class="px-6 py-3 text-right cursor-pointer select-none hover:text-white transition-colors" @click="toggleSort('price_change_percentage_24h')">
            <span class="inline-flex items-center gap-1 justify-end">24h <SortIcon :active="sortKey === 'price_change_percentage_24h'" :asc="sortAsc" /></span>
          </th>
          <th class="px-6 py-3 text-right cursor-pointer select-none hover:text-white transition-colors" @click="toggleSort('market_cap')">
            <span class="inline-flex items-center gap-1 justify-end">Market Cap <SortIcon :active="sortKey === 'market_cap'" :asc="sortAsc" /></span>
          </th>
          <th class="px-6 py-3 text-right cursor-pointer select-none hover:text-white transition-colors" @click="toggleSort('total_volume')">
            <span class="inline-flex items-center gap-1 justify-end">Volume (24h) <SortIcon :active="sortKey === 'total_volume'" :asc="sortAsc" /></span>
          </th>
        </tr>
      </thead>
      <tbody>
        <!-- Skeleton rows -->
        <template v-if="loading">
          <tr v-for="i in 10" :key="i" class="bg-dark border-b border-dark-border">
            <td class="px-6 py-4"><div class="h-4 w-6 bg-dark-border rounded-full animate-pulse" /></td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-dark-border rounded-full animate-pulse" />
                <div class="h-4 w-28 bg-dark-border rounded-full animate-pulse" />
              </div>
            </td>
            <td class="px-6 py-4"><div class="h-4 w-24 bg-dark-border rounded-full animate-pulse ml-auto" /></td>
            <td class="px-6 py-4"><div class="h-4 w-16 bg-dark-border rounded-full animate-pulse ml-auto" /></td>
            <td class="px-6 py-4"><div class="h-4 w-20 bg-dark-border rounded-full animate-pulse ml-auto" /></td>
            <td class="px-6 py-4"><div class="h-4 w-20 bg-dark-border rounded-full animate-pulse ml-auto" /></td>
          </tr>
        </template>

        <!-- Data rows -->
        <template v-else>
          <NuxtLink
            v-for="coin in sortedCoins"
            :key="coin.id"
            :to="`/crypto/${coin.id}`"
            custom
            v-slot="{ navigate }"
          >
            <tr
              class="bg-dark border-b border-dark-border hover:bg-dark-lighter cursor-pointer transition-colors"
              @click="navigate"
            >
              <td class="px-6 py-4 text-muted">{{ coin.market_cap_rank }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <img :src="coin.image" :alt="coin.name" class="w-8 h-8 rounded-full" />
                  <div>
                    <span class="font-medium text-white">{{ coin.name }}</span>
                    <span class="ml-2 text-xs text-muted uppercase">{{ coin.symbol }}</span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-right font-medium text-white">{{ formatCurrency(coin.current_price) }}</td>
              <td
                class="px-6 py-4 text-right font-medium"
                :class="coin.price_change_percentage_24h >= 0 ? 'text-green-500' : 'text-red-500'"
              >
                {{ formatPercentage(coin.price_change_percentage_24h) }}
              </td>
              <td class="px-6 py-4 text-right text-gray-300">{{ formatLargeNumber(coin.market_cap) }}</td>
              <td class="px-6 py-4 text-right text-gray-300">{{ formatLargeNumber(coin.total_volume) }}</td>
            </tr>
          </NuxtLink>
        </template>
      </tbody>
    </table>
  </div>

  <!-- Mobile: card layout -->
  <div class="md:hidden space-y-3">
    <!-- Skeleton cards -->
    <template v-if="loading">
      <div v-for="i in 10" :key="i" class="bg-dark-light rounded-lg border border-dark-border p-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-dark-border rounded-full animate-pulse" />
            <div class="space-y-2">
              <div class="h-4 w-24 bg-dark-border rounded-full animate-pulse" />
              <div class="h-3 w-16 bg-dark-border rounded-full animate-pulse" />
            </div>
          </div>
          <div class="space-y-2">
            <div class="h-4 w-20 bg-dark-border rounded-full animate-pulse ml-auto" />
            <div class="h-3 w-14 bg-dark-border rounded-full animate-pulse ml-auto" />
          </div>
        </div>
      </div>
    </template>

    <!-- Data cards -->
    <template v-else>
      <NuxtLink
        v-for="coin in sortedCoins"
        :key="coin.id"
        :to="`/crypto/${coin.id}`"
        class="block bg-dark-light rounded-lg border border-dark-border p-4 hover:border-accent/30 transition-colors"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <img :src="coin.image" :alt="coin.name" class="w-10 h-10 rounded-full" />
            <div>
              <div class="font-medium text-white">{{ coin.name }}</div>
              <div class="text-xs text-muted uppercase">{{ coin.symbol }} · #{{ coin.market_cap_rank }}</div>
            </div>
          </div>
          <div class="text-right">
            <div class="font-medium text-white">{{ formatCurrency(coin.current_price) }}</div>
            <div
              class="text-sm font-medium"
              :class="coin.price_change_percentage_24h >= 0 ? 'text-green-500' : 'text-red-500'"
            >
              {{ formatPercentage(coin.price_change_percentage_24h) }}
            </div>
          </div>
        </div>
      </NuxtLink>
    </template>
  </div>
</template>

<script setup>
const props = defineProps({
  coins: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})

const sortKey = ref('market_cap_rank')
const sortAsc = ref(true)

function toggleSort(key) {
  if (sortKey.value === key) {
    sortAsc.value = !sortAsc.value
  } else {
    sortKey.value = key
    sortAsc.value = key === 'market_cap_rank'
  }
}

const sortedCoins = computed(() => {
  if (!props.coins) return []
  return [...props.coins].sort((a, b) => {
    const aVal = a[sortKey.value] ?? 0
    const bVal = b[sortKey.value] ?? 0
    return sortAsc.value ? aVal - bVal : bVal - aVal
  })
})

const { formatCurrency, formatLargeNumber, formatPercentage } = useFormatters()
</script>
