<script setup lang="ts">
import { Search, Bell, Settings } from 'lucide-vue-next'

defineEmits<{
  logout: []
}>()
</script>

<template>
  <header class="app-header">
    <div class="header-left">
      <div class="search-box">
        <Search size="18" />
        <input type="text" placeholder="Search..." />
      </div>
    </div>

    <div class="header-right">
      <button class="header-btn" title="Notifications">
        <Bell size="20" />
        <span class="notification-dot" />
      </button>
      <button class="header-btn" title="Settings">
        <Settings size="20" />
      </button>
    </div>
  </header>
</template>

<style scoped>
.app-header {
  background-color: var(--color-bg);
  border-bottom: 1px solid var(--color-border-light);
  padding: var(--space-lg);
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.header-left {
  flex: 1;
  display: flex;
  align-items: center;
}

.search-box {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  padding: 8px 14px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border-light);
  border-radius: var(--radius-input);
  max-width: 300px;
  width: 100%;
}

.search-box svg {
  color: var(--color-primary);
  flex-shrink: 0;
}

.search-box input {
  flex: 1;
  background: none;
  border: none;
  outline: none;
  font-size: var(--font-size-sm);
  color: var(--color-text);
}

.search-box input::placeholder {
  color: var(--color-text-muted);
}

.header-right {
  display: flex;
  align-items: center;
  gap: var(--space-lg);
  margin-left: var(--space-xl);
}

.header-btn {
  background: none;
  border: none;
  color: var(--color-primary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
  position: relative;
  padding: 6px;
}

.header-btn:hover {
  color: var(--color-primary-hover);
}

.notification-dot {
  position: absolute;
  top: 0;
  right: 0;
  width: 8px;
  height: 8px;
  background-color: var(--color-danger);
  border-radius: 50%;
}
</style>
