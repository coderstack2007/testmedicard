<template>
  <div class="base-input-wrapper">
    <label v-if="label" :for="id" class="base-input__label">{{ label }}</label>
    <div class="base-input__container">
      <component v-if="iconLeft" :is="iconLeft" class="base-input__icon base-input__icon--left" />
      <input
        :id="id"
        :class="['base-input', { 'base-input--error': error }]"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      />
      <component v-if="iconRight" :is="iconRight" class="base-input__icon base-input__icon--right" />
    </div>
    <p v-if="error" class="base-input__error">{{ error }}</p>
    <p v-if="hint && !error" class="base-input__hint">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  modelValue?: string   // ← добавить
  label?: string
  placeholder?: string
  type?: string
  error?: string
  hint?: string
  disabled?: boolean
  iconLeft?: any
  iconRight?: any
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',       // ← добавить
  type: 'text',
  disabled: false,
})

// ← добавить emit
defineEmits<{
  'update:modelValue': [value: string]
}>()

const id = computed(() => `input-${Math.random().toString(36).substr(2, 9)}`)
</script>
<style scoped>
.base-input-wrapper {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.base-input__label {
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--color-text);
}

.base-input__container {
  position: relative;
  display: flex;
  align-items: center;
}

.base-input {
  width: 100%;
  padding: 10px 14px;
  font-size: var(--font-size-base);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-input);
  background-color: var(--color-bg);
  color: var(--color-text);
  transition: var(--transition);
  font-family: var(--font-family);
}

.base-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px var(--color-primary-light);
}

.base-input--error {
  border-color: var(--color-danger);
}

.base-input--error:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.base-input:disabled {
  background-color: var(--color-surface);
  color: var(--color-text-muted);
  cursor: not-allowed;
}

.base-input__icon {
  position: absolute;
  width: 20px;
  height: 20px;
  color: var(--color-primary);
  pointer-events: none;
}

.base-input__icon--left {
  left: 12px;
}

.base-input__icon--right {
  right: 12px;
}

.base-input__error {
  margin: 0;
  font-size: var(--font-size-sm);
  color: var(--color-danger);
}

.base-input__hint {
  margin: 0;
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
}
</style>
