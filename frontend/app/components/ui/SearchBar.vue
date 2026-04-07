<template>
  <div class="relative" ref="wrapper">
    <div class="relative">
      <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted pointer-events-none" />
      <input
        v-model="query"
        type="text"
        placeholder="Search coins..."
        class="w-full rounded-lg border border-dark-border bg-dark pl-10 pr-4 py-2 text-sm text-white placeholder-muted focus:border-accent/30 focus:ring-1 focus:ring-accent/30 focus:outline-none"
        @input="onInput"
        @focus="onFocus"
        @keydown.escape="close"
      />
    </div>

    <!-- Search results dropdown -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 -translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-1"
    >
      <div
        v-if="showResults"
        class="absolute top-full left-0 right-0 mt-1 bg-dark-light border border-dark-border rounded-lg shadow-xl shadow-black/30 max-h-80 overflow-y-auto z-50"
      >
        <div class="px-4 py-2 text-xs font-medium text-muted uppercase border-b border-dark-border">
          {{ query.length >= 2 ? 'Search Results' : 'Popular Coins' }}
        </div>

        <!-- Skeleton rows while searching -->
        <template v-if="searching">
          <div v-for="i in 5" :key="i" class="flex items-center gap-3 px-4 py-3">
            <div class="w-6 h-6 bg-dark-border rounded-full animate-pulse" />
            <div class="flex-1 space-y-1">
              <div class="h-3 w-24 bg-dark-border rounded-full animate-pulse" />
            </div>
            <div class="h-3 w-8 bg-dark-border rounded-full animate-pulse" />
          </div>
        </template>

        <!-- Results -->
        <TransitionGroup
          v-else
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="opacity-0 translate-x-2"
          enter-to-class="opacity-100 translate-x-0"
          move-class="transition duration-150 ease-out"
        >
          <NuxtLink
            v-for="coin in displayResults"
            :key="coin.id"
            :to="`/crypto/${coin.id}`"
            class="flex items-center gap-3 px-4 py-3 hover:bg-dark-lighter transition-colors"
            @click="close"
          >
            <img :src="coin.thumb" :alt="coin.name" class="w-6 h-6 rounded-full" />
            <div>
              <span class="text-sm font-medium text-white">{{ coin.name }}</span>
              <span class="ml-2 text-xs text-muted uppercase">{{ coin.symbol }}</span>
            </div>
            <span v-if="coin.market_cap_rank" class="ml-auto text-xs text-muted bg-dark rounded-full px-2 py-0.5">#{{ coin.market_cap_rank }}</span>
          </NuxtLink>
        </TransitionGroup>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'

const query = ref('')
const results = ref([])
const trendingCache = ref([])
const showResults = ref(false)
const searching = ref(false)
const focused = ref(false)

defineExpose({ focused })
const wrapper = ref(null)
let debounceTimer

const config = useRuntimeConfig()
const baseUrl = import.meta.server ? config.apiBaseUrl : config.public.apiBaseUrl

// Preload trending coins for the default dropdown
async function loadTrending() {
  if (trendingCache.value.length) return
  try {
    const response = await $fetch(`${baseUrl}/api/cryptocurrencies/trending`)
    if (response.success) {
      trendingCache.value = response.data.slice(0, 6).map((c) => ({
        id: c.item.id,
        name: c.item.name,
        symbol: c.item.symbol,
        thumb: c.item.thumb,
        market_cap_rank: c.item.market_cap_rank,
      }))
    }
  } catch {
    // Silent fail — trending is a nice-to-have
  }
}

// Show trending on focus when input is empty
function onFocus() {
  focused.value = true
  loadTrending()
  if (query.value.length < 2) {
    showResults.value = true
  }
}

// Results to display: search results if searching, trending as fallback
const displayResults = computed(() => {
  if (query.value.length >= 2 && results.value.length) return results.value
  if (query.value.length < 2) return trendingCache.value
  return []
})

function onInput() {
  clearTimeout(debounceTimer)

  if (query.value.length < 2) {
    results.value = []
    showResults.value = trendingCache.value.length > 0
    searching.value = false
    return
  }

  searching.value = true

  debounceTimer = setTimeout(async () => {
    try {
      const response = await $fetch(
        `${baseUrl}/api/cryptocurrencies/search`,
        { params: { q: query.value } }
      )
      if (response.success) {
        results.value = response.data.slice(0, 8)
        showResults.value = true
      }
    } catch {
      results.value = []
    } finally {
      searching.value = false
    }
  }, 300)
}

function close() {
  showResults.value = false
  query.value = ''
  results.value = []
  searching.value = false
  focused.value = false
}

function onClickOutside(e) {
  if (wrapper.value && !wrapper.value.contains(e.target)) {
    close()
  }
}

onMounted(() => document.addEventListener('click', onClickOutside))
onUnmounted(() => document.removeEventListener('click', onClickOutside))
</script>
