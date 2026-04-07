<template>
  <ChevronUpIcon v-if="active && asc" class="w-4 h-4 text-white" />
  <ChevronDownIcon v-else-if="active && !asc" class="w-4 h-4 text-white" />
  <ChevronUpDownIcon v-else class="w-4 h-4 text-muted" />
</template>

<script setup>
import { ChevronUpIcon, ChevronDownIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'

defineProps({
  active: { type: Boolean, required: true },
  asc: { type: Boolean, required: true },
})
</script>
