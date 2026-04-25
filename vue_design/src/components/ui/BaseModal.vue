<template>
  <div class="base-modal-overlay" @click="closeIfOutside">
    <div class="base-modal" role="dialog" aria-modal="true">
      <div class="base-modal__header">
        <h2 class="base-modal__title">{{ title }}</h2>
        <button
          class="base-modal__close"
          @click="$emit('close')"
          aria-label="Close modal"
        >
          <X size="20" />
        </button>
      </div>
      <div class="base-modal__body">
        <slot />
      </div>
      <div v-if="$slots.footer" class="base-modal__footer">
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { X } from 'lucide-vue-next'

interface Props {
  title: string
  closeOnOutside?: boolean
}

withDefaults(defineProps<Props>(), {
  closeOnOutside: true,
})

const emit = defineEmits<{
  close: []
}>()

const closeIfOutside = (event: MouseEvent) => {
  if ((event.target as HTMLElement).classList.contains('base-modal-overlay')) {
    emit('close')
  }
}
</script>

<style scoped>
.base-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.base-modal {
  background-color: var(--color-bg);
  border-radius: var(--radius-card);
  box-shadow: var(--shadow-modal);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.base-modal__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-lg);
  border-bottom: 1px solid var(--color-border-light);
}

.base-modal__title {
  margin: 0;
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--color-text);
}

.base-modal__close {
  background: none;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
  padding: 4px;
}

.base-modal__close:hover {
  color: var(--color-primary);
}

.base-modal__body {
  padding: var(--space-lg);
}

.base-modal__footer {
  padding: var(--space-lg);
  border-top: 1px solid var(--color-border-light);
  display: flex;
  gap: var(--space-md);
  justify-content: flex-end;
}
</style>
