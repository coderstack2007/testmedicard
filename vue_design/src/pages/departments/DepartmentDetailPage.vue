<template>
  <AppLayout>
    <div class="page-container">
      <router-link :to="`/branches/${department?.branch_id}`" class="back-link">
        <ChevronLeft :size="18" />
        Back to Branch
      </router-link>

      <LoadingSpinner v-if="isLoading" message="Loading department..." />

      <div v-else-if="department" class="dept-detail">
        <div class="detail-header">
          <h1>{{ department.name }}</h1>
        </div>

        <BaseCard title="Department Information">
          <div class="info-grid">
            <div class="info-item">
              <span class="label">Description</span>
              <span class="value">{{ department.description || '—' }}</span>
            </div>
            <div class="info-item">
              <span class="label">Branch</span>
              <span class="value">{{ department.branch?.name || '—' }}</span>
            </div>
          </div>
        </BaseCard>

        <!-- Edit / Delete actions -->
        <div class="actions">
          <BaseButton variant="ghost" @click="openEdit">
            <Pencil :size="16" /> Edit
          </BaseButton>
          <BaseButton variant="danger" @click="confirmDelete">
            <Trash2 :size="16" /> Delete
          </BaseButton>
        </div>
      </div>

      <div v-else class="not-found">
        <AlertCircle :size="48" />
        <p>Department not found</p>
      </div>
    </div>

    <!-- Edit Modal -->
    <BaseModal v-if="showModal" :open="showModal" @close="showModal = false" title="Edit Department">
      <form @submit.prevent="handleUpdate" class="modal-form">
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
          <BaseButton type="button" variant="ghost" @click="showModal = false">Cancel</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSubmitting">Save</BaseButton>
        </div>
      </form>
    </BaseModal>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import * as departmentsApi from '@/api/departments'
import AppLayout from '@/components/layout/AppLayout.vue'
import BaseCard from '@/components/ui/BaseCard.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { ChevronLeft, AlertCircle, Pencil, Trash2 } from 'lucide-vue-next'
import type { Department } from '@/types'

const route = useRoute()
const router = useRouter()
const deptId = parseInt(route.params.id as string)

const department = ref<Department | null>(null)
const isLoading = ref(true)
const showModal = ref(false)
const isSubmitting = ref(false)
const formError = ref('')

const formData = ref({ name: '', description: '' })

const openEdit = () => {
  formData.value = {
    name: department.value?.name || '',
    description: department.value?.description || '',
  }
  formError.value = ''
  showModal.value = true
}

const handleUpdate = async () => {
  if (!formData.value.name.trim()) {
    formError.value = 'Name is required'
    return
  }
  isSubmitting.value = true
  formError.value = ''
  try {
    const result = await departmentsApi.updateDepartment(deptId, formData.value)
    department.value = result.data ?? result
    showModal.value = false
  } catch (error: any) {
    formError.value =
      error.response?.data?.message ||
      error.response?.data?.error ||
      error.message ||
      'Error updating department'
  } finally {
    isSubmitting.value = false
  }
}

const confirmDelete = async () => {
  if (!confirm(`Delete "${department.value?.name}"?`)) return
  try {
    await departmentsApi.deleteDepartment(deptId)
    router.push(`/branches/${department.value?.branch_id}`)
  } catch (error: any) {
    alert(error.response?.data?.message || 'Error deleting department')
  }
}

onMounted(async () => {
  try {
    const result = await departmentsApi.getDepartment(deptId)
    console.log('✅ Department:', result)
    department.value = result
  } catch (error) {
    console.error('❌ Error fetching department:', error)
  } finally {
    isLoading.value = false
  }
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
  width: fit-content;
}
.dept-detail {
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
.actions {
  display: flex;
  gap: var(--space-md);
}
.not-found {
  text-align: center;
  padding: var(--space-4xl);
  color: var(--color-neutral-500);
}
.not-found svg { margin-bottom: var(--space-md); opacity: 0.5; }
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