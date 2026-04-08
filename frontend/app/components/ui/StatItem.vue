<template>
  <div>
    <p class="text-sm text-muted">{{ label }}</p>
    <p class="text-lg font-semibold text-white">{{ value }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: String, required: true },
})
</script>
