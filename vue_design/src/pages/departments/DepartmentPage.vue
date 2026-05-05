<template>
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

      <div class="actions" v-if="canManage">
        <BaseButton variant="ghost" @click="openEdit">
          <Pencil :size="16" /> Edit
        </BaseButton>
        <BaseButton variant="danger" @click="confirmDelete">
          <Trash2 :size="16" /> Delete
        </BaseButton>
      </div>

      <BaseModal v-if="showDeleteModal" :open="showDeleteModal" @close="showDeleteModal = false" title="Delete Department">
        <div class="modal-form">
          <div v-if="deleteError" class="error-box">{{ deleteError }}</div>
          <p v-else>Are you sure you want to delete <strong>{{ department?.name }}</strong>?</p>
          <div class="modal-actions">
            <BaseButton variant="ghost" @click="showDeleteModal = false">Cancel</BaseButton>
            <BaseButton v-if="!deleteError" variant="danger" @click="handleDelete">Delete</BaseButton>
          </div>
        </div>
      </BaseModal>

      <!-- Navigation -->
      <div class="nav-cards">
        <div class="nav-card" @click="router.push(`/departments/${deptId}/doctors`)">
          <Stethoscope :size="32" />
          <span>Doctors</span>
        </div>
        <div class="nav-card" @click="router.push(`/departments/${deptId}/patients`)">
          <Users :size="32" />
          <span>Patients</span>
        </div>
      </div>
    </div>

    <div v-else class="not-found">
      <AlertCircle :size="48" />
      <p>Department not found</p>
    </div>
  </div>

  <!-- Edit Department Modal -->
  <BaseModal v-if="showEditModal" :open="showEditModal" @close="showEditModal = false" title="Edit Department">
    <form @submit.prevent="handleUpdate" class="modal-form">
      <BaseInput v-model="editForm.name" label="Department Name" placeholder="General Medicine" required />
      <BaseInput v-model="editForm.description" label="Description" placeholder="Department description" />
      <div v-if="editError" class="error-box">{{ editError }}</div>
      <div class="modal-actions">
        <BaseButton type="button" variant="ghost" @click="showEditModal = false">Cancel</BaseButton>
        <BaseButton type="submit" variant="primary" :loading="isSubmitting">Save</BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import * as departmentsApi from '@/api/departments'

import BaseCard from '@/components/ui/BaseCard.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { ChevronLeft, AlertCircle, Pencil, Trash2, Stethoscope, Users } from 'lucide-vue-next'
import type { Department } from '@/types'

const route  = useRoute()
const router = useRouter()
const auth   = useAuthStore()
const deptId = parseInt(route.params.id as string)

const canManage = computed(() =>
  ['moderator', 'absolute_admin'].includes(auth.user?.role ?? '')
)

const department    = ref<Department | null>(null)
const isLoading     = ref(true)
const showEditModal = ref(false)
const isSubmitting  = ref(false)
const editError     = ref('')
const editForm      = ref({ name: '', description: '' })
const showDeleteModal = ref(false)
const deleteError = ref('')

const openEdit = () => {
  editForm.value = { name: department.value?.name || '', description: department.value?.description || '' }
  editError.value = ''
  showEditModal.value = true
}

const handleUpdate = async () => {
  if (!editForm.value.name.trim()) { editError.value = 'Name is required'; return }
  isSubmitting.value = true
  editError.value = ''
  try {
    const result = await departmentsApi.updateDepartment(deptId, editForm.value)
    const updated = result.data ?? result
    department.value = { ...department.value, ...updated }
    showEditModal.value = false
  } catch (error: any) {
    editError.value = error.response?.data?.message || error.message || 'Error updating'
  } finally {
    isSubmitting.value = false
  }
}

const confirmDelete = () => {
  deleteError.value = ''
  showDeleteModal.value = true
}

const handleDelete = async () => {
  try {
    await departmentsApi.deleteDepartment(deptId)
    router.push(`/branches/${department.value?.branch_id}`)
  } catch (error: any) {
    deleteError.value = error.response?.data?.message || error.message || 'Error deleting'
  } finally {
    showDeleteModal.value = false
  }
}

onMounted(async () => {
  try {
    department.value = await departmentsApi.getDepartment(deptId)
  } catch (error) {
    console.error('❌ Error fetching department:', error)
  } finally {
    isLoading.value = false
  }
})
</script>

<style scoped>
.page-container { display: flex; flex-direction: column; gap: var(--space-lg); }

.back-link {
  display: inline-flex; align-items: center; gap: var(--space-xs);
  color: var(--color-primary); text-decoration: none; font-weight: 500; width: fit-content;
}

.dept-detail { display: flex; flex-direction: column; gap: var(--space-lg); }
.detail-header h1 { margin: 0; font-size: var(--font-3xl); }

.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--space-lg); }
.info-item { display: flex; flex-direction: column; gap: var(--space-xs); }
.label { font-size: var(--font-xs); text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-neutral-500); font-weight: 600; }
.value { font-size: var(--font-base); color: var(--color-neutral-900); }

.actions { display: flex; gap: var(--space-md); }

.nav-cards {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--space-lg);
  margin-top: var(--space-md);
}

.nav-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: var(--space-md);
  padding: var(--space-2xl) var(--space-lg);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-card);
  cursor: pointer;
  transition: var(--transition);
  font-size: var(--font-base);
  font-weight: 600;
  color: var(--color-text);
}

.nav-card:hover {
  border-color: var(--color-primary);
  box-shadow: var(--shadow-card);
  color: var(--color-primary);
  transform: translateY(-2px);
}

.not-found { text-align: center; padding: var(--space-4xl); color: var(--color-neutral-500); }
.not-found svg { margin-bottom: var(--space-md); opacity: 0.5; }

.modal-form { display: flex; flex-direction: column; gap: var(--space-lg); padding: var(--space-lg) 0; }
.modal-actions { display: flex; gap: var(--space-md); justify-content: flex-end; }

.error-box {
  background: #fee2e2; border: 1px solid #fecaca;
  border-radius: var(--radius-btn); padding: var(--space-md);
  font-size: var(--font-sm); color: #991b1b;
}
</style>