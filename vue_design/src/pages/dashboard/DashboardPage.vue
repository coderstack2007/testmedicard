<template>
  <AppLayout>
    <div class="dashboard-container">
      <!-- Header Section -->
      <div class="dashboard-header">
        <div class="header-content">
          <h1>{{ greeting }}, {{ authStore.user?.name }}</h1>
          <p class="header-subtitle">Welcome to Medicard</p>
        </div>
        <div class="header-badge">
          <BaseBadge :variant="getRoleBadgeVariant()">
            {{ formatRole(authStore.user?.role || '') }}
          </BaseBadge>
        </div>
      </div>

      <!-- Role-Based Content -->
      <div v-if="isPatient" class="dashboard-grid">
        <BaseCard title="Active Diagnoses" :count="stats.activeDiagnoses" />
        <BaseCard title="Upcoming Consultations" :count="stats.consultations" />
        <BaseCard title="Messages" :count="stats.messages" highlight />
        <BaseCard title="Medical Records" :count="stats.records" />
      </div>

      <div v-else-if="isDoctor" class="dashboard-grid">
        <BaseCard title="My Patients" :count="stats.myPatients" />
        <BaseCard title="Today's Appointments" :count="stats.todayAppointments" />
        <BaseCard title="Pending Diagnoses" :count="stats.pendingDiagnoses" highlight />
        <BaseCard title="Messages" :count="stats.messages" />
      </div>

      <div v-else-if="isModerator" class="dashboard-grid">
        <BaseCard title="Total Patients" :count="stats.totalPatients" />
        <BaseCard title="Total Doctors" :count="stats.totalDoctors" />
        <BaseCard title="Active Cases" :count="stats.activeCases" highlight />
        <BaseCard title="Departments" :count="stats.departments" />
      </div>

      <div v-else-if="isAdmin" class="dashboard-grid">
        <BaseCard title="Total Users" :count="stats.totalUsers" />
        <BaseCard title="Active Branches" :count="stats.activeBranches" />
        <BaseCard title="System Health" :count="stats.systemHealth" />
        <BaseCard title="API Usage" :count="stats.apiUsage" />
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
          <router-link
            v-if="isPatient"
            to="/chats"
            class="action-button"
          >
            <MessageSquare :size="24" />
            <span>Start Consultation</span>
          </router-link>
          <router-link
            v-if="isDoctor"
            to="/patients"
            class="action-button"
          >
            <Users :size="24" />
            <span>View My Patients</span>
          </router-link>
          <router-link
            v-if="isModerator"
            to="/branches"
            class="action-button"
          >
            <Building2 :size="24" />
            <span>Manage Branches</span>
          </router-link>
          <router-link
            v-if="isAdmin"
            to="/branches"
            class="action-button"
          >
            <BarChart3 :size="24" />
            <span>View Analytics</span>
          </router-link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

import BaseCard from '@/components/ui/BaseCard.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import { MessageSquare, Users, Building2, BarChart3 } from 'lucide-vue-next'

const authStore = useAuthStore()

const stats = reactive({
  // Patient stats
  activeDiagnoses: 2,
  consultations: 1,
  messages: 5,
  records: 12,

  // Doctor stats
  myPatients: 8,
  todayAppointments: 3,
  pendingDiagnoses: 2,

  // Moderator stats
  totalPatients: 156,
  totalDoctors: 24,
  activeCases: 45,
  departments: 8,

  // Admin stats
  totalUsers: 250,
  activeBranches: 5,
  systemHealth: 98,
  apiUsage: 85,
})

const isPatient = computed(() => authStore.user?.role === 'patient')
const isDoctor = computed(() => authStore.user?.role === 'doctor')
const isModerator = computed(() => authStore.user?.role === 'moderator')
const isAdmin = computed(() => authStore.user?.role === 'absolute_admin')

const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Good Morning'
  if (hour < 18) return 'Good Afternoon'
  return 'Good Evening'
})

const formatRole = (role: string): string => {
  const roleMap: Record<string, string> = {
    patient: 'Patient',
    doctor: 'Doctor',
    moderator: 'Moderator',
    absolute_admin: 'Administrator',
  }
  return roleMap[role] || role
}

const getRoleBadgeVariant = () => {
  switch (authStore.user?.role) {
    case 'patient':
      return 'info'
    case 'doctor':
      return 'success'
    case 'moderator':
      return 'warning'
    case 'absolute_admin':
      return 'danger'
    default:
      return 'primary'
  }
}

onMounted(() => {
  // TODO: Fetch actual stats from API
  // await dashboardApi.getStats()
})
</script>

<style scoped>
.dashboard-container {
  display: flex;
  flex-direction: column;
  gap: var(--space-2xl);
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--space-lg) var(--space-xl);
  background: linear-gradient(135deg, var(--color-primary), var(--color-info));
  border-radius: var(--radius-card);
  color: white;
  box-shadow: var(--shadow-card);
}

.header-content h1 {
  font-size: var(--font-2xl);
  font-weight: 600;
  margin: 0 0 var(--space-xs) 0;
}

.header-subtitle {
  font-size: var(--font-sm);
  opacity: 0.9;
  margin: 0;
}

.header-badge {
  display: flex;
  gap: var(--space-md);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: var(--space-lg);
}

.quick-actions {
  margin-top: var(--space-xl);
}

.quick-actions h2 {
  font-size: var(--font-lg);
  font-weight: 600;
  color: var(--color-neutral-900);
  margin: 0 0 var(--space-lg) 0;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: var(--space-lg);
}

.action-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: var(--space-md);
  padding: var(--space-lg);
  background: var(--color-white);
  border: 2px solid var(--color-neutral-200);
  border-radius: var(--radius-card);
  color: var(--color-neutral-700);
  text-decoration: none;
  transition: all var(--transition-normal);
  cursor: pointer;
}

.action-button:hover {
  border-color: var(--color-primary);
  background: var(--color-neutral-50);
  color: var(--color-primary);
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
}

.action-button svg {
  color: currentColor;
}

.action-button span {
  font-weight: 500;
  font-size: var(--font-sm);
}

@media (max-width: 768px) {
  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--space-md);
  }

  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
