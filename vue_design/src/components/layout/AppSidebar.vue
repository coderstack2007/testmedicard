<script setup lang="ts">
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  Home,
  BarChart3,
  MessageSquare,
  Users,
  Stethoscope,
  Building2,
  LogOut,
} from 'lucide-vue-next'

interface NavItem {
  label: string
  path: string
  icon: any
  roles?: string[]
}

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const props = defineProps({
  open: Boolean,
})

const navItems = computed<NavItem[]>(() => {
  const baseItems: NavItem[] = [{ label: 'Dashboard', path: '/dashboard', icon: Home }]

  if (auth.isPatient) {
    return [
      ...baseItems,
      { label: 'My Diagnoses', path: '/diagnoses', icon: BarChart3 },
      { label: 'Chat with Doctor', path: '/chats', icon: MessageSquare },
    ]
  }

  if (auth.isDoctor) {
    return [
      ...baseItems,
      { label: 'Patients', path: '/patients', icon: Users },
      { label: 'Chats', path: '/chats', icon: MessageSquare },
    ]
  }

  if (auth.isModerator) {
    return [
      ...baseItems,
      { label: 'Doctors', path: '/doctors', icon: Stethoscope },
      { label: 'Patients', path: '/patients', icon: Users },
      { label: 'Branches', path: '/departments', icon: Building2 },
    ]
  }

  if (auth.isAdmin) {
    return [
      ...baseItems,
      { label: 'Branches', path: '/branches', icon: Building2 },
      { label: 'Doctors', path: '/doctors', icon: Stethoscope },
      { label: 'Patients', path: '/patients', icon: Users },
    ]
  }

  return baseItems
})

const isActive = (path: string) => {
  return route.path === path || route.path.startsWith(path.split('/:')[0])
}

async function handleLogout() {
  await auth.logout()
  await router.push('/login')
}
</script>

<template>
  <aside class="sidebar" :class="{ open }">
    <div class="sidebar-header">
      <h1>Medicard</h1>
    </div>

    <nav class="sidebar-nav">
      <router-link
        v-for="item in navItems"
        :key="item.path"
        :to="item.path"
        class="nav-item"
        :class="{ active: isActive(item.path) }"
      >
        <component :is="item.icon" class="nav-icon" />
        <span class="nav-label">{{ item.label }}</span>
      </router-link>
    </nav>

    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar">{{ auth.user?.name.charAt(0).toUpperCase() }}</div>
        <div>
          <div class="user-name">{{ auth.user?.name }}</div>
          <div class="user-role">{{ auth.user?.role }}</div>
        </div>
      </div>
      <button class="logout-btn" @click="handleLogout" title="Logout">
        <LogOut size="20" />
      </button>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  width: 280px;
  background: linear-gradient(180deg, #0f172a 0%, #1a1f3a 100%);
  color: white;
  display: flex;
  flex-direction: column;
  border-right: 1px solid rgba(255, 255, 255, 0.05);
  overflow-y: auto;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
  padding: var(--space-lg);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header h1 {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  margin: 0;
  background: linear-gradient(135deg, #2563eb, #0ea5e9);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.sidebar-nav {
  flex: 1;
  padding: var(--space-md) 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  padding: var(--space-md) var(--space-lg);
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: var(--transition);
  cursor: pointer;
}

.nav-item:hover {
  background-color: var(--color-sidebar-hover);
  color: white;
}

.nav-item.active {
  background: linear-gradient(90deg, var(--color-primary), transparent);
  color: white;
  border-left: 3px solid var(--color-info);
  padding-left: calc(var(--space-lg) - 3px);
}

.nav-icon {
  width: 20px;
  height: 20px;
  color: var(--color-primary);
  flex-shrink: 0;
}

.nav-item.active .nav-icon {
  color: var(--color-info);
}

.nav-label {
  font-size: var(--font-size-sm);
  font-weight: 500;
  white-space: nowrap;
}

.sidebar-footer {
  padding: var(--space-lg);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-md);
}

.user-info {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  min-width: 0;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary), var(--color-info));
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: var(--font-size-lg);
  flex-shrink: 0;
}

.user-name {
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: white;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: var(--font-size-xs);
  color: rgba(255, 255, 255, 0.6);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.logout-btn {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
  padding: 4px;
  flex-shrink: 0;
}

.logout-btn:hover {
  color: var(--color-danger);
}

.nav-label {
  font-size: 14px;
  font-weight: 500;
}

.sidebar-footer {
  padding: 16px 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  transition: var(--transition);

  &:hover {
    background-color: var(--color-sidebar-hover);
  }
}

.user-avatar {
  width: 40px;
  height: 40px;
  background-color: var(--color-primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  flex-shrink: 0;
}

.user-name {
  font-size: 13px;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.6);
  text-transform: capitalize;
}

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    z-index: 100;
    transform: translateX(-100%);
    transition: transform var(--transition);

    &.open {
      transform: translateX(0);
    }
  }
}
</style>
