<template>
  <span :class="['base-badge', `base-badge--${variant}`, `base-badge--${size}`]">
    <slot />
  </span>
</template>

<script setup lang="ts">
interface Props {
  variant?: 'success' | 'warning' | 'danger' | 'info' | 'primary'
  size?: 'sm' | 'md'
}

withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
})
</script>

<style scoped>
.base-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  font-weight: 500;
  font-size: var(--font-size-sm);
}

.base-badge--sm {
  padding: 4px 10px;
}

.base-badge--md {
  padding: 6px 14px;
}

.base-badge--success {
  background-color: rgba(16, 185, 129, 0.1);
  color: var(--color-success);
}

.base-badge--warning {
  background-color: rgba(245, 158, 11, 0.1);
  color: var(--color-warning);
}

.base-badge--danger {
  background-color: rgba(239, 68, 68, 0.1);
  color: var(--color-danger);
}

.base-badge--info {
  background-color: rgba(14, 165, 233, 0.1);
  color: var(--color-info);
}

.base-badge--primary {
  background-color: var(--color-primary-light);
  color: var(--color-primary);
}
</style>
