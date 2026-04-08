<template>
  <div
    class="rounded-lg border px-4 py-3 flex items-center gap-3"
    :class="inline
      ? 'border-red-500/20 bg-red-500/5'
      : 'border-dark-border bg-dark-light justify-center flex-col py-8'"
  >
    <ExclamationTriangleIcon class="w-5 h-5 text-red-400 flex-shrink-0" />
    <p class="text-sm text-gray-400">{{ message }}</p>
    <button
      class="text-sm font-medium text-accent hover:text-accent-hover transition-colors"
      :class="inline ? 'ml-auto' : ''"
      @click="$emit('retry')"
    >
      Retry
    </button>
  </div>
</template>

<script setup>
import { ExclamationTriangleIcon } from '@heroicons/vue/20/solid'

defineProps({
  message: { type: String, required: true },
  inline: { type: Boolean, default: false },
})
defineEmits(['retry'])
</script>
