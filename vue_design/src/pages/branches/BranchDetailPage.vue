<template>

    <div class="page-container">
      <router-link to="/branches" class="back-link">
        <ChevronLeft :size="18" />
        Back to Branches
      </router-link>

      <LoadingSpinner v-if="isLoading" message="Loading branch details..." />

      <div v-else-if="branch" class="branch-detail">
        <div class="detail-header">
          <h1>{{ branch.name }}</h1>
        </div>

        <BaseCard title="Branch Information" class="info-card">
          <div class="info-grid">
            <div class="info-item">
              <span class="label">Address</span>
              <span class="value">{{ branch.address || '—' }}</span>
            </div>
          
          </div>
        </BaseCard>

        <!-- Departments Section -->
        <div class="section-header">
          <h2>Departments</h2>
          <BaseButton variant="primary" @click="openCreateDepartment">
            <Plus :size="18" />
            Add Department
          </BaseButton>
        </div>

        <LoadingSpinner v-if="isDeptLoading" message="Loading departments..." />

        <template v-else>
          <div v-if="departments.length > 0" class="departments-grid">
            <div
              v-for="dept in departments"
              :key="dept.id"
              class="dept-card"
              @click="router.push(`/departments/${dept.id}`)"
            >
              <div class="dept-header">
                <h3>{{ dept.name }}</h3>
              </div>
              <p class="dept-description">{{ dept.description || 'No description' }}</p>
            </div>
          </div>

          <div v-else class="empty-state">
            <Building2 :size="48" />
            <p>No departments found</p>
          </div>
        </template>
      </div>

      <div v-else class="not-found">
        <AlertCircle :size="48" />
        <p>Branch not found</p>
      </div>
    </div>

    <!-- Create Department Modal -->
    <BaseModal v-if="showModal" :open="showModal" @close="showModal = false" title="Add New Department">
      <form @submit.prevent="handleSubmit" class="modal-form">
        <BaseInput
          v-model="formData.name"
          label="Department Name"
          placeholder="General Medicine"
          required
        />
        <BaseInput
          v-model="formData.description"
          label="Description"
          placeholder="Department description"
        />
        <div v-if="formError" class="error-box">{{ formError }}</div>
        <div class="modal-actions">
          <BaseButton type="button" variant="ghost" @click="showModal = false">
            Cancel
          </BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSubmitting">
            Create Department
          </BaseButton>
        </div>
      </form>
    </BaseModal>

</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import * as branchesApi from '@/api/branches'
import * as departmentsApi from '@/api/departments'

import BaseCard from '@/components/ui/BaseCard.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { ChevronLeft, AlertCircle, Plus, Building2 } from 'lucide-vue-next'
import type { Branch, Department } from '@/types'

const route = useRoute()
const router = useRouter()
const branchId = parseInt(route.params.id as string)

const branch = ref<Branch | null>(null)
const departments = ref<Department[]>([])
const isLoading = ref(true)
const isDeptLoading = ref(false)
const showModal = ref(false)
const isSubmitting = ref(false)
const formError = ref('')

const formData = ref({
  name: '',
  description: '',
  
})

const openCreateDepartment = () => {
  formData.value = { name: '', description: '' }
  formError.value = ''
  showModal.value = true
}

const handleSubmit = async () => {
  if (!formData.value.name.trim()) {
    formError.value = 'Department name is required'
    return
  }

  isSubmitting.value = true
  formError.value = ''

  try {
    const result = await departmentsApi.createDepartment(branchId, formData.value)
    console.log('✅ Department created:', result)
    showModal.value = false
    await fetchDepartments()
  } catch (error: any) {
    formError.value =
      error.response?.data?.message ||
      error.response?.data?.error ||
      error.message ||
      'Error creating department'
  } finally {
    isSubmitting.value = false
  }
}

const fetchDepartments = async () => {
  isDeptLoading.value = true
  try {
    const response = await departmentsApi.getDepartments(branchId)
    departments.value = response.data?.data || response.data || []
  } catch (error) {
    console.error('❌ Error fetching departments:', error)
    departments.value = []
  } finally {
    isDeptLoading.value = false
  }
}

onMounted(async () => {
  try {
    const response = await branchesApi.getBranch(branchId)
    branch.value = response.data?.data || response.data || response
  } catch (error) {
    console.error('❌ Error fetching branch:', error)
  } finally {
    isLoading.value = false
  }

  await fetchDepartments()
})
</script>

<style scoped>
.page-container {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: var(--space-xs);
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 500;
  transition: all var(--transition-fast);
  width: fit-content;
}

.back-link:hover { gap: var(--space-sm); }

.branch-detail {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.detail-header h1 {
  margin: 0;
  font-size: var(--font-3xl);
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--space-lg);
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: var(--space-xs);
}

.label {
  font-size: var(--font-xs);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--color-neutral-500);
  font-weight: 600;
}

.value {
  font-size: var(--font-base);
  color: var(--color-neutral-900);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.section-header h2 {
  margin: 0;
  font-size: var(--font-xl);
  font-weight: 600;
}

.departments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: var(--space-lg);
}

.dept-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-card);
  padding: var(--space-lg);
  cursor: pointer;
  transition: var(--transition);
}

.dept-card:hover {
  border-color: var(--color-primary);
  box-shadow: var(--shadow-card);
  transform: translateY(-2px);
}

.dept-header {
  margin-bottom: var(--space-sm);
}

.dept-header h3 {
  margin: 0;
  font-size: var(--font-lg);
}

.dept-description {
  font-size: var(--font-sm);
  color: var(--color-neutral-500);
  margin: 0;
}

.empty-state {
  text-align: center;
  padding: var(--space-4xl) var(--space-2xl);
  color: var(--color-neutral-500);
}

.empty-state svg {
  margin-bottom: var(--space-md);
  opacity: 0.5;
}

.not-found {
  text-align: center;
  padding: var(--space-4xl) var(--space-2xl);
  color: var(--color-neutral-500);
}

.not-found svg {
  margin-bottom: var(--space-md);
  opacity: 0.5;
}

.modal-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
  padding: var(--space-lg) 0;
}

.modal-actions {
  display: flex;
  gap: var(--space-md);
  justify-content: flex-end;
  margin-top: var(--space-lg);
}

.error-box {
  background: #fee2e2;
  border: 1px solid #fecaca;
  border-radius: var(--radius-btn);
  padding: var(--space-md);
  font-size: var(--font-sm);
  color: #991b1b;
}
</style>