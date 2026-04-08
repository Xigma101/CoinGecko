<template>
  <div>
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
      <NuxtImg :src="coin.image?.large" :alt="coin.name" width="64" height="64" class="w-16 h-16 rounded-full" />
      <div>
        <h1 class="text-3xl font-bold text-white">{{ coin.name }}</h1>
        <span class="text-sm text-muted uppercase">{{ coin.symbol }} · Rank #{{ coin.market_cap_rank }}</span>
      </div>
    </div>

    <!-- Price + Stats grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Current price card -->
      <Card class="lg:col-span-1">
        <p class="text-sm text-muted mb-1">Current Price</p>
        <p class="text-3xl font-bold text-white">{{ formatCurrency(coin.market_data?.current_price?.[cc]) }}</p>
        <p
          class="text-sm font-medium mt-1"
          :class="(coin.market_data?.price_change_percentage_24h ?? 0) >= 0 ? 'text-green-500' : 'text-red-500'"
        >
          {{ formatPercentage(coin.market_data?.price_change_percentage_24h) }} (24h)
        </p>
      </Card>

      <!-- Market stats -->
      <Card class="lg:col-span-2">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
          <StatItem label="Market Cap" :value="formatLargeNumber(coin.market_data?.market_cap?.[cc])" />
          <StatItem label="24h Volume" :value="formatLargeNumber(coin.market_data?.total_volume?.[cc])" />
          <StatItem label="Circulating Supply" :value="formatSupply(coin.market_data?.circulating_supply)" />
          <StatItem label="24h High" :value="formatCurrency(coin.market_data?.high_24h?.[cc])" />
          <StatItem label="24h Low" :value="formatCurrency(coin.market_data?.low_24h?.[cc])" />
          <StatItem v-if="coin.market_data?.max_supply" label="Max Supply" :value="formatSupply(coin.market_data.max_supply)" />
        </div>
      </Card>
    </div>

    <!-- Slot for additional content (e.g. price chart) -->
    <div v-if="$slots.default" class="mb-8">
      <slot />
    </div>

    <!-- Description -->
    <Card v-if="coin.description?.en">
      <h2 class="text-lg font-semibold text-white mb-3">About {{ coin.name }}</h2>
      <div class="text-sm text-gray-300 leading-relaxed prose prose-sm prose-invert max-w-none" v-html="coin.description.en" />
    </Card>
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
