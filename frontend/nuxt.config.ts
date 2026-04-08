// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  future: {
    compatibilityVersion: 4
  },

  modules: ['@nuxtjs/tailwindcss', 'nuxt-charts', '@nuxt/image', '@vueuse/nuxt'],

  components: [
    { path: '~/components/ui', pathPrefix: false },
    { path: '~/components/crypto', pathPrefix: false },
  ],

  css: ['~/assets/css/main.css'],

  vite: {
    server: {
      watch: {
        usePolling: true,
        interval: 1000,
      },
    },
  },

  runtimeConfig: {
    // Server-side only — used by Nuxt server routes to proxy to Laravel
    apiBaseUrl: process.env.API_BASE_URL || 'http://localhost:8000',
  },
})
