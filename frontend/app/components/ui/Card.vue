<template>
  <div class="bg-dark-light rounded-lg border border-dark-border p-4 sm:p-6" :class="$attrs.class">
    <slot />
  </div>
</template>

<script setup>
defineOptions({ inheritAttrs: false })
</script>
