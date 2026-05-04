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
        <div v-if="lastDeptPage > 1" class="pagination">
  <BaseButton
    variant="ghost"
    :disabled="currentDeptPage === 1"
    @click="fetchDepartments(currentDeptPage - 1)"
  >
    ← Prev
  </BaseButton>

  <button
    v-for="page in lastDeptPage"
    :key="page"
    class="page-btn"
    :class="{ active: page === currentDeptPage }"
    @click="fetchDepartments(page)"
  >
    {{ page }}
  </button>

  <BaseButton
    variant="ghost"
    :disabled="currentDeptPage === lastDeptPage"
    @click="fetchDepartments(currentDeptPage + 1)"
  >
    Next →
  </BaseButton>
</div> 
      </div>
  <div class="actions" v-if="canManage">
        <BaseButton variant="danger" @click="confirmDelete">
          <Trash2 :size="16" /> Delete Branch
        </BaseButton>
      </div>

      <!-- Delete Modal — перед закрывающим </template> -->
      <BaseModal v-if="showDeleteModal" :open="showDeleteModal" @close="showDeleteModal = false" title="Delete Branch">
        <div class="modal-form">
          <div v-if="deleteError" class="error-box">{{ deleteError }}</div>
          <p v-else>Are you sure you want to delete <strong>{{ branch?.name }}</strong>?</p>
          <div class="modal-actions">
            <BaseButton variant="ghost" @click="showDeleteModal = false">Cancel</BaseButton>
            <BaseButton v-if="!deleteError" variant="danger" @click="handleDelete">Delete</BaseButton>
          </div>
        </div>
      </BaseModal>
  
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
import { ref, onMounted, computed  } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import * as branchesApi from '@/api/branches'
import * as departmentsApi from '@/api/departments'

import BaseCard from '@/components/ui/BaseCard.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { ChevronLeft, AlertCircle, Plus, Building2 , Trash2 } from 'lucide-vue-next'
import type { Branch, Department } from '@/types'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const branchId = parseInt(route.params.id as string)
const showDeleteModal = ref(false)
const deleteError = ref('')

const branch = ref<Branch | null>(null)
const departments = ref<Department[]>([])
const isLoading = ref(true)
const isDeptLoading = ref(false)
const showModal = ref(false)
const isSubmitting = ref(false)
const formError = ref('')

const currentDeptPage = ref(1)
const lastDeptPage = ref(1)



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

const fetchDepartments = async (page = 1) => {
  isDeptLoading.value = true
  try {
    const response = await departmentsApi.getDepartments(branchId, { page })
    departments.value = response.data?.data || response.data || []
    currentDeptPage.value = response.data?.meta?.current_page ?? 1
    lastDeptPage.value = response.data?.meta?.last_page ?? 1
  } catch (error) {
    console.error('❌ Error fetching departments:', error)
    departments.value = []
  } finally {
    isDeptLoading.value = false
  }
}

const confirmDelete = () => {
  deleteError.value = ''
  if (departments.value.length > 0) {
    deleteError.value = `Cannot delete: branch has ${departments.value.length} department(s). Remove all departments first.`
  }
  showDeleteModal.value = true
}

const handleDelete = async () => {
  try {
    await branchesApi.deleteBranch(branchId)
    router.push('/branches')
  } catch (error: any) {
    deleteError.value = error.response?.data?.message || error.message || 'Error deleting'
  }
}

const auth = useAuthStore()
const canManage = computed(() =>
  ['moderator', 'absolute_admin'].includes(auth.user?.role ?? '')
)
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


.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  margin-top: var(--space-lg);
}

.page-btn {
  width: 36px;
  height: 36px;
  border-radius: var(--radius-btn);
  border: 1px solid var(--color-border);
  background: var(--color-surface);
  cursor: pointer;
  font-size: var(--font-sm);
  color: var(--color-neutral-900);
  transition: var(--transition);
}

.page-btn.active {
  background: var(--color-primary);
  color: white;
  border-color: var(--color-primary);
}

.page-btn:hover:not(.active) {
  border-color: var(--color-primary);
}
</style> 