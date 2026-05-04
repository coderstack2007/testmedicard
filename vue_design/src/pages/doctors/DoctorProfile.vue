<template>
  <div class="page-container">
    <router-link :to="`/departments/${doctor?.department_id}`" class="back-link">
    <ChevronLeft :size="18" /> Back to Department
  </router-link>

    <LoadingSpinner v-if="isLoading" message="Loading doctor..." />

    <div v-else-if="doctor" class="profile">
      <div class="profile-header">
        <div class="avatar-lg">{{ initials(doctor.user?.name) }}</div>
        <div>
          <h1>{{ doctor.user?.name }}</h1>
          <p class="specialization">{{ doctor.specialization || 'No specialization' }}</p>
        </div>
      </div>

      <BaseCard title="Doctor Information">
        <div class="info-grid">
          <div class="info-item">
            <span class="label">Email</span>
            <span class="value">{{ doctor.user?.email || '—' }}</span>
          </div>
          <div class="info-item">
            <span class="label">Phone</span>
            <span class="value">{{ doctor.phone || '—' }}</span>
          </div>
          <div class="info-item">
            <span class="label">Branch</span>
            <span class="value">{{ doctor.branch?.name || '—' }}</span>
          </div>
          <div class="info-item">
            <span class="label">Department</span>
            <span class="value">{{ doctor.department?.name || '—' }}</span>
          </div>
        </div>
      </BaseCard>

      <div class="actions" v-if="canManage">
        <BaseButton variant="ghost" @click="openEdit">
          <Pencil :size="16" /> Edit
        </BaseButton>
        <BaseButton variant="danger" @click="showDeleteModal = true">
          <Trash2 :size="16" /> Delete
        </BaseButton>
      </div>
    </div>

    <div v-else class="not-found">
      <AlertCircle :size="48" />
      <p>Doctor not found</p>
    </div>
  </div>

  <!-- Edit Modal -->
  <BaseModal v-if="showEditModal" :open="showEditModal" @close="showEditModal = false" title="Edit Doctor">
    <form @submit.prevent="handleUpdate" class="modal-form">
      <BaseInput v-model="editForm.specialization" label="Specialization" placeholder="Cardiology" />
      <BaseInput v-model="editForm.phone" label="Phone" placeholder="+998901234567" />
      <div v-if="editError" class="error-box">{{ editError }}</div>
      <div class="modal-actions">
        <BaseButton type="button" variant="ghost" @click="showEditModal = false">Cancel</BaseButton>
        <BaseButton type="submit" variant="primary" :loading="isSubmitting">Save</BaseButton>
      </div>
    </form>
  </BaseModal>

  <!-- Delete Modal -->
  <BaseModal v-if="showDeleteModal" :open="showDeleteModal" @close="showDeleteModal = false" title="Delete Doctor">
    <div class="modal-form">
      <p>Are you sure you want to delete <strong>{{ doctor?.user?.name }}</strong>?</p>
      <div class="modal-actions">
        <BaseButton variant="ghost" @click="showDeleteModal = false">Cancel</BaseButton>
        <BaseButton variant="danger" @click="handleDelete">Delete</BaseButton>
      </div>
    </div>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import * as doctorsApi from '@/api/doctors'

import BaseCard from '@/components/ui/BaseCard.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { ChevronLeft, AlertCircle, Pencil, Trash2 } from 'lucide-vue-next'

const route    = useRoute()
const router   = useRouter()
const auth     = useAuthStore()
const doctorId = parseInt(route.params.id as string)

const canManage = computed(() =>
  ['moderator', 'absolute_admin'].includes(auth.user?.role ?? '')
)

const doctor        = ref<any>(null)
const isLoading     = ref(true)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const isSubmitting  = ref(false)
const editError     = ref('')
const editForm      = ref({ specialization: '', phone: '' })

const initials = (name?: string) =>
  name ? name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) : '?'

const openEdit = () => {
  editForm.value = {
    specialization: doctor.value?.specialization || '',
    phone: doctor.value?.phone || '',
  }
  editError.value = ''
  showEditModal.value = true
}

const handleUpdate = async () => {
  isSubmitting.value = true
  editError.value = ''
  try {
    const result = await doctorsApi.updateDoctor(doctorId, editForm.value)
    doctor.value = { ...doctor.value, ...result }
    showEditModal.value = false
  } catch (error: any) {
    editError.value = error.response?.data?.message || error.message || 'Error updating'
  } finally {
    isSubmitting.value = false
  }
}

const handleDelete = async () => {
  try {
    await doctorsApi.deleteDoctor(doctorId)
    router.push(`/departments/${doctor.value?.department_id}`)
  } catch (error: any) {
    showDeleteModal.value = false
  }
}

onMounted(async () => {
  try {
    doctor.value = await doctorsApi.getDoctor(doctorId)
  } catch (error) {
    console.error('❌ Error fetching doctor:', error)
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

.profile { display: flex; flex-direction: column; gap: var(--space-lg); }

.profile-header {
  display: flex; align-items: center; gap: var(--space-lg);
}

.avatar-lg {
  width: 72px; height: 72px; border-radius: 50%;
  background: var(--color-primary); color: white;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 24px; flex-shrink: 0;
}

.profile-header h1 { margin: 0; font-size: var(--font-3xl); }
.specialization { color: var(--color-primary); font-weight: 500; margin: 4px 0 0; }

.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--space-lg); }
.info-item { display: flex; flex-direction: column; gap: var(--space-xs); }
.label { font-size: var(--font-xs); text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-neutral-500); font-weight: 600; }
.value { font-size: var(--font-base); color: var(--color-neutral-900); }

.actions { display: flex; gap: var(--space-md); }

.not-found { text-align: center; padding: var(--space-4xl); color: var(--color-neutral-500); }

.modal-form { display: flex; flex-direction: column; gap: var(--space-lg); padding: var(--space-lg) 0; }
.modal-actions { display: flex; gap: var(--space-md); justify-content: flex-end; }

.error-box {
  background: #fee2e2; border: 1px solid #fecaca;
  border-radius: var(--radius-btn); padding: var(--space-md);
  font-size: var(--font-sm); color: #991b1b;
}
</style>