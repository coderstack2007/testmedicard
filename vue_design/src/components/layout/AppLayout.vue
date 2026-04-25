<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppHeader from '@/components/layout/AppHeader.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const auth = useAuthStore()
const router = useRouter()
const sidebarOpen = ref(true)

const isLoading = computed(() => !auth.user)
</script>

<template>
  <div class="layout">
    <AppSidebar :open="sidebarOpen" />
    <div class="layout-main">
      <AppHeader />
      <main class="layout-content">
        <RouterView v-if="!isLoading" />
        <LoadingSpinner v-else show message="Loading..." />
      </main>
    </div>
  </div>
</template>

<style scoped>
.layout {
  display: flex;
  height: 100vh;
  background-color: var(--color-bg);
}

.layout-main {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.layout-content {
  overflow: hidden;
}

.layout-content {
  flex: 1;
  overflow-y: auto;
  padding: var(--space-xl);
  background-color: var(--color-surface);

}

</style>