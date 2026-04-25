<template>
  <button
    :class="[
      'base-button',
      `base-button--${variant}`,
      `base-button--${size}`,
      { 'base-button--loading': loading, 'base-button--disabled': disabled },
    ]"
    :disabled="disabled || loading"
    v-bind="$attrs"
  >
    <slot />
    <span v-if="loading" class="base-button__spinner" />
  </button>
</template>

<script setup lang="ts">
interface Props {
  variant?: 'primary' | 'secondary' | 'danger' | 'ghost'
  size?: 'sm' | 'md' | 'lg'
  loading?: boolean
  disabled?: boolean
}

withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  loading: false,
  disabled: false,
})
</script>

<style scoped>
.base-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: var(--transition);
  border-radius: var(--radius-btn);
  white-space: nowrap;
  font-family: var(--font-family);
}

/* Sizes */
.base-button--sm {
  padding: 6px 12px;
  font-size: var(--font-size-sm);
}

.base-button--md {
  padding: 10px 16px;
  font-size: var(--font-size-base);
}

.base-button--lg {
  padding: 12px 24px;
  font-size: var(--font-size-lg);
}

/* Variants */
.base-button--primary {
  background-color: var(--color-primary);
  color: white;
}

.base-button--primary:hover:not(.base-button--disabled) {
  background-color: var(--color-primary-hover);
  box-shadow: var(--shadow-hover);
}

.base-button--secondary {
  background-color: var(--color-surface);
  color: var(--color-text);
  border: 1px solid var(--color-border);
}

.base-button--secondary:hover:not(.base-button--disabled) {
  background-color: var(--color-surface-secondary);
  border-color: var(--color-primary);
}

.base-button--danger {
  background-color: var(--color-danger);
  color: white;
}

.base-button--danger:hover:not(.base-button--disabled) {
  background-color: #dc2626;
}

.base-button--ghost {
  background-color: transparent;
  color: var(--color-primary);
}

.base-button--ghost:hover:not(.base-button--disabled) {
  background-color: var(--color-primary-light);
}

/* States */
.base-button--disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.base-button--loading {
  color: transparent;
  position: relative;
}

.base-button__spinner {
  position: absolute;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
