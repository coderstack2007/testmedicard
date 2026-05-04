<template>

    <div class="page-container">
      <div class="page-header">
        <h1>Branches</h1>
        <BaseButton variant="primary" @click="newBranch">
          <Plus :size="18" />
          Add Branch
        </BaseButton>
      </div>

      <LoadingSpinner v-if="isLoading" message="Loading branches..." />

      <div v-else class="branches-grid">
        <div
          v-for="branch in branches"
          :key="branch.id"
          class="branch-card"
          @click="selectBranch(branch.id)"
        >
          <BaseCard class="cursor-pointer">
            <div class="card-header">
              <h3>{{ branch.name }}</h3>
            </div>
            <p class="branch-code">
              <Building2 :size="16" /> Code: {{ branch.code }}
            </p>
            <p class="branch-address">
              <MapPin :size="16" /> {{ branch.address || 'No address' }}
            </p>
          </BaseCard>
        </div>
      </div>

      <div v-if="branches.length === 0 && !isLoading" class="empty-state">
        <Building2 :size="48" />
        <p>No branches found</p>
      </div>

      <BaseModal v-if="showModal" :open="showModal" @close="showModal = false" title="Add New Branch">
        <form @submit.prevent="handleSubmit" class="modal-form">
          <BaseInput
            v-model="formData.name"
            label="Branch Name"
            placeholder="Main Medical Center"
            required
          />
          <BaseInput
            v-model="formData.address"
            label="Address"
            placeholder="123 Medical St, Health City"
          />
          <BaseInput
            v-model="formData.code"
            label="Branch Code"
            placeholder="MAIN-01"
            required
          />
          <div class="modal-actions">
            <BaseButton type="button" variant="ghost" @click="showModal = false">
              Cancel
            </BaseButton>
            <BaseButton type="submit" variant="primary" :loading="isSubmitting">
              Create Branch
            </BaseButton>
          </div>
        </form>
      </BaseModal>
      <div v-if="lastPage > 1" class="pagination">
  <BaseButton
    variant="ghost"
    :disabled="currentPage === 1"
    @click="fetchBranches(currentPage - 1)"
  >
    ← Prev
  </BaseButton>

  <button
    v-for="page in lastPage"
    :key="page"
    class="page-btn"
    :class="{ active: page === currentPage }"
    @click="fetchBranches(page)"
  >
    {{ page }}
  </button>

  <BaseButton
    variant="ghost"
    :disabled="currentPage === lastPage"
    @click="fetchBranches(currentPage + 1)"
  >
    Next →
  </BaseButton>
</div>
    </div>

</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import * as branchesApi from '@/api/branches'

import BaseCard from '@/components/ui/BaseCard.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { Plus, MapPin, Phone, Building2 } from 'lucide-vue-next'
import type { Branch } from '@/types'


const currentPage = ref(1)
const lastPage = ref(1)

const router = useRouter()

const branches = ref<Branch[]>([])
const isLoading = ref(false)
const showModal = ref(false)
const isSubmitting = ref(false)
const formData = ref({
  name: '',
  address: '',
  code: '',
  
})

const selectBranch = (id: number) => {
  router.push(`/branches/${id}`)
}

const newBranch = () => {
  formData.value = { name: '', address: '', code: '' }
  showModal.value = true
}

const handleSubmit = async () => {
  console.log('📋 formData:', formData.value)

  if (!formData.value.name || !formData.value.code) {
    console.warn('❌ Validation failed — name or code missing')
    return
  }

  isSubmitting.value = true
  try {
    console.log('🚀 Creating branch...')
    const result = await branchesApi.createBranch(formData.value)
    console.log('✅ Branch created:', result)
    showModal.value = false
    await fetchBranches()
  } catch (error: any) {
  console.error('❌ RAW ERROR:', error)
  console.error('❌ TYPE:', typeof error)
  console.error('❌ KEYS:', Object.keys(error || {}))
  alert(JSON.stringify(error?.response?.data || error?.message || error))

  } finally {
    isSubmitting.value = false
  }
  
}

const fetchBranches = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await branchesApi.getBranches({ page })
    branches.value = response.data.data
    currentPage.value = response.data.meta.current_page
    lastPage.value = response.data.meta.last_page
  } catch (error) {
    console.error('❌ Error fetching branches:', error)
    branches.value = []
  } finally {
    isLoading.value = false
  }
}
onMounted(() => {
  fetchBranches(1)
})
</script>

<style scoped>
.page-container {
  display: flex;
  flex-direction: column;
  gap: var(--space-xl);
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-header h1 {
  font-size: var(--font-2xl);
  font-weight: 600;
  margin: 0;
}

.branches-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: var(--space-lg);
}

.branch-card {
  cursor: pointer;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: var(--space-md);
}

.card-header h3 {
  margin: 0;
  font-size: var(--font-lg);
}

.branch-code,
.branch-address {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  font-size: var(--font-sm);
  color: var(--color-neutral-600);
  margin: var(--space-xs) 0;
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

.cursor-pointer {
  cursor: pointer;
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
