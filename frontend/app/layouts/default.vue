<template>
  <div class="min-h-screen bg-dark">
    <header class="bg-dark-light border-b border-dark-border sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo / App title -->
          <NuxtLink
            to="/"
            class="flex items-center gap-2 shrink-0 transition-all duration-200"
            :class="searchFocused ? 'sm:flex hidden' : ''"
          >
            <span class="text-xl font-bold text-white hidden sm:inline">CryptoTracker</span>
            <span class="text-xl font-bold text-white sm:hidden">CT</span>
          </NuxtLink>

          <!-- Search bar + Currency selector -->
          <div class="flex items-center gap-2 flex-1 max-w-lg transition-all duration-200" :class="searchFocused ? 'mx-0 sm:mx-8' : 'mx-2 sm:mx-8'">
            <div class="flex-1">
              <SearchBar ref="searchBar" />
            </div>
            <CurrencySelector :class="searchFocused ? 'hidden sm:block' : ''" />
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>

    <footer class="bg-dark-light border-t border-dark-border mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <p class="text-center text-sm text-muted">
          Powered by <a href="https://www.coingecko.com/" target="_blank" class="text-accent hover:underline">CoinGecko API</a>
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
const searchBar = ref(null)
const searchFocused = computed(() => searchBar.value?.focused ?? false)
</script>
