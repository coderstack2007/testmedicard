<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

onMounted(() => {
  auth.loadFromStorage()
})
</script>

<template>
  <RouterView />
</template>

<style>
:root {
  --color-primary: #2563EB;
  --color-primary-hover: #1D4ED8;
  --color-success: #10B981;
  --color-warning: #F59E0B;
  --color-danger: #EF4444;
  --color-bg: #F8FAFC;
  --color-surface: #FFFFFF;
  --color-sidebar: #0F172A;
  --color-sidebar-hover: #1E293B;
  --color-text: #0F172A;
  --color-text-muted: #64748B;
  --color-border: #E2E8F0;
  --radius-card: 12px;
  --radius-btn: 8px;
  --shadow-card: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
  --transition: 0.15s ease;
  --font-main: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body, #app {
  width: 100%;
  height: 100%;
}

body {
  font-family: var(--font-main);
  background-color: var(--color-bg);
  color: var(--color-text);
  line-height: 1.5;
}
</style>
