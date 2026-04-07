<template>
  <div class="relative" ref="wrapper">
    <button
      class="flex items-center gap-1 rounded-lg border border-dark-border bg-dark px-3 py-2 text-sm font-medium text-gray-300 hover:bg-dark-lighter transition-colors focus:outline-none focus:ring-1 focus:ring-accent/30"
      @click="open = !open"
    >
      {{ currentCurrency.symbol }} {{ currentCurrency.code.toUpperCase() }}
      <ChevronDownIcon class="w-4 h-4 text-muted" />
    </button>

    <div
      v-if="open"
      class="absolute right-0 mt-1 w-48 bg-dark-light border border-dark-border rounded-lg shadow-xl shadow-black/30 max-h-64 overflow-y-auto z-50"
    >
      <button
        v-for="c in CURRENCIES"
        :key="c.code"
        class="w-full text-left px-4 py-2 text-sm hover:bg-dark-lighter transition-colors flex items-center justify-between"
        :class="c.code === currency ? 'text-accent font-medium' : 'text-gray-300'"
        @click="select(c.code)"
      >
        <span>{{ c.symbol }} {{ c.name }}</span>
        <span class="text-xs text-muted uppercase">{{ c.code }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ChevronDownIcon } from '@heroicons/vue/20/solid'

const { currency, currentCurrency, CURRENCIES } = useCurrency()
const open = ref(false)
const wrapper = ref(null)

function select(code) {
  currency.value = code
  open.value = false
}

function onClickOutside(e) {
  if (wrapper.value && !wrapper.value.contains(e.target)) {
    open.value = false
  }
}

onMounted(() => document.addEventListener('click', onClickOutside))
onUnmounted(() => document.removeEventListener('click', onClickOutside))
</script>
