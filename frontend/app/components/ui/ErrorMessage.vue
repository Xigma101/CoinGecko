<template>
  <div class="text-center">
    <p class="text-red-500 font-medium mb-2">Something went wrong</p>
    <p class="text-sm text-muted mb-4">{{ message }}</p>
    <button
      class="px-4 py-2 text-sm font-semibold text-dark bg-accent rounded-lg hover:bg-accent-hover transition-colors"
      @click="$emit('retry')"
    >
      Try again
    </button>
  </div>
</template>

<script setup>
defineProps({
  message: { type: String, required: true },
})
defineEmits(['retry'])
</script>
