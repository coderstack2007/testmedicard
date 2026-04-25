<template>
  <div :class="['base-card', { 'base-card--interactive': interactive }]">
    <slot />
  </div>
</template>

<script setup lang="ts">
interface Props {
  interactive?: boolean
}

withDefaults(defineProps<Props>(), {
  interactive: false,
})
</script>

<style scoped>
.base-card {
  background-color: var(--color-bg);
  border: 1px solid var(--color-border-light);
  border-radius: var(--radius-card);
  padding: var(--space-lg);
  box-shadow: var(--shadow-card);
  transition: var(--transition);
}

.base-card--interactive {
  cursor: pointer;
}

.base-card--interactive:hover {
  box-shadow: var(--shadow-hover);
  border-color: var(--color-primary-light);
}
</style>
