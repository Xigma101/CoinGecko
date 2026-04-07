<template>
  <div>
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
      <img :src="coin.image?.large" :alt="coin.name" class="w-16 h-16 rounded-full" />
      <div>
        <h1 class="text-3xl font-bold text-white">{{ coin.name }}</h1>
        <span class="text-sm text-muted uppercase">{{ coin.symbol }} · Rank #{{ coin.market_cap_rank }}</span>
      </div>
    </div>

    <!-- Price + Stats grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Current price card -->
      <div class="bg-dark-light rounded-lg border border-dark-border p-6 lg:col-span-1">
        <p class="text-sm text-muted mb-1">Current Price</p>
        <p class="text-3xl font-bold text-white">{{ formatCurrency(coin.market_data?.current_price?.[cc]) }}</p>
        <p
          class="text-sm font-medium mt-1"
          :class="(coin.market_data?.price_change_percentage_24h ?? 0) >= 0 ? 'text-green-500' : 'text-red-500'"
        >
          {{ formatPercentage(coin.market_data?.price_change_percentage_24h) }} (24h)
        </p>
      </div>

      <!-- Market stats -->
      <div class="bg-dark-light rounded-lg border border-dark-border p-6 lg:col-span-2">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
          <div>
            <p class="text-sm text-muted">Market Cap</p>
            <p class="text-lg font-semibold text-white">{{ formatLargeNumber(coin.market_data?.market_cap?.[cc]) }}</p>
          </div>
          <div>
            <p class="text-sm text-muted">24h Volume</p>
            <p class="text-lg font-semibold text-white">{{ formatLargeNumber(coin.market_data?.total_volume?.[cc]) }}</p>
          </div>
          <div>
            <p class="text-sm text-muted">Circulating Supply</p>
            <p class="text-lg font-semibold text-white">{{ formatSupply(coin.market_data?.circulating_supply) }}</p>
          </div>
          <div>
            <p class="text-sm text-muted">24h High</p>
            <p class="text-lg font-semibold text-white">{{ formatCurrency(coin.market_data?.high_24h?.[cc]) }}</p>
          </div>
          <div>
            <p class="text-sm text-muted">24h Low</p>
            <p class="text-lg font-semibold text-white">{{ formatCurrency(coin.market_data?.low_24h?.[cc]) }}</p>
          </div>
          <div v-if="coin.market_data?.max_supply">
            <p class="text-sm text-muted">Max Supply</p>
            <p class="text-lg font-semibold text-white">{{ formatSupply(coin.market_data.max_supply) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Slot for additional content (e.g. price chart) -->
    <div v-if="$slots.default" class="mb-8">
      <slot />
    </div>

    <!-- Description -->
    <div v-if="coin.description?.en" class="bg-dark-light rounded-lg border border-dark-border p-6">
      <h2 class="text-lg font-semibold text-white mb-3">About {{ coin.name }}</h2>
      <div class="text-sm text-gray-300 leading-relaxed prose prose-sm prose-invert max-w-none" v-html="coin.description.en" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  coin: { type: Object, required: true },
})

const { currency } = useCurrency()
const cc = computed(() => currency.value)
const { formatCurrency, formatLargeNumber, formatPercentage, formatSupply } = useFormatters()
</script>
