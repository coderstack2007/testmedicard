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

        <!-- Dept edit/delete only for moderator+ -->
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
        <!-- Doctors Section -->
        <div class="section-header">
          <h2>Doctors <span class="count">{{ filteredDoctors.length }}</span></h2>
          <!-- Add Doctor only for moderator / absolute_admin -->
          <BaseButton v-if="canManage" variant="primary" @click="openCreateDoctor">
            <Plus :size="16" /> Add Doctor
          </BaseButton>
        </div>

        <!-- Search — searches only within THIS department's doctors -->
        <div class="search-bar">
          <Search :size="16" class="search-icon" />
          <input
            v-model="searchQuery"
            type="text"
            class="search-input"
            placeholder="Search by name or specialization..."
          />
          <button v-if="searchQuery" class="search-clear" @click="searchQuery = ''">
            <X :size="14" />
          </button>
        </div>

        <LoadingSpinner v-if="isDoctorsLoading" message="Loading doctors..." />

        <template v-else>
          <div v-if="filteredDoctors.length > 0" class="doctors-grid">
            <div v-for="doctor in filteredDoctors" :key="doctor.id" class="doctor-card"   @click="router.push(`/doctors/${doctor.id}`)"   style="cursor: pointer" >
              <div class="doctor-avatar">{{ initials(doctor.user?.name) }}</div>
              <div class="doctor-info">
                <h3>{{ doctor.user?.name }}</h3>
                <p class="specialization">{{ doctor.specialization || 'No specialization' }}</p>
                <p class="phone" v-if="doctor.phone">📞 {{ doctor.phone }}</p>
              </div>
            </div>
          </div>

          <div v-else class="empty-state">
            <Stethoscope :size="48" />
            <p v-if="searchQuery">No doctors match "{{ searchQuery }}"</p>
            <p v-else>No doctors in this department</p>
          </div>
        </template>
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
<div v-if="lastPage > 1" class="pagination">
  <BaseButton
    variant="ghost"
    :disabled="currentPage === 1"
    @click="fetchDoctors(currentPage - 1)"
  >
    ← Prev
  </BaseButton>

  <button
    v-for="page in lastPage"
    :key="page"
    class="page-btn"
    :class="{ active: page === currentPage }"
    @click="fetchDoctors(page)"
  >
    {{ page }}
  </button>

  <BaseButton
    variant="ghost"
    :disabled="currentPage === lastPage"
    @click="fetchDoctors(currentPage + 1)"
  >
    Next →
  </BaseButton>
</div>
    <!-- Create Doctor Modal -->
    <BaseModal v-if="showDoctorModal" :open="showDoctorModal" @close="showDoctorModal = false" title="Add Doctor">
      <form @submit.prevent="handleCreateDoctor" class="modal-form">
        <!-- Context: auto-filled, not shown as inputs -->
        <div class="context-badge">
          <span>🏥 {{ department?.branch?.name }}</span>
          <span class="sep">›</span>
          <span>🏬 {{ department?.name }}</span>
        </div>

        <BaseInput v-model="doctorForm.name"          label="Full Name"       placeholder="Dr. John Smith"     required />
        <BaseInput v-model="doctorForm.email"         label="Email"           placeholder="doctor@medicard.uz" type="email" required />
        <BaseInput v-model="doctorForm.password"      label="Password"        placeholder="••••••••"           type="password" required />
        <BaseInput v-model="doctorForm.specialization" label="Specialization" placeholder="Cardiology" />
        <BaseInput v-model="doctorForm.phone"         label="Phone"           placeholder="+998901234567" />

        <div v-if="doctorError" class="error-box">{{ doctorError }}</div>
        <div class="modal-actions">
          <BaseButton type="button" variant="ghost" @click="showDoctorModal = false">Cancel</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isDoctorSubmitting">Create Doctor</BaseButton>
        </div>
      </form>
    </BaseModal>

</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import * as departmentsApi from '@/api/departments'
import * as doctorsApi from '@/api/doctors'
import * as usersApi from '@/api/users'


import BaseCard from '@/components/ui/BaseCard.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { ChevronLeft, AlertCircle, Pencil, Trash2, Plus, Stethoscope, Search, X } from 'lucide-vue-next'
import type { Department } from '@/types'

const route  = useRoute()
const router = useRouter()
const auth   = useAuthStore()
const deptId = parseInt(route.params.id as string)

// Only moderator and absolute_admin can manage
const canManage = computed(() =>
  ['moderator', 'absolute_admin'].includes(auth.user?.role ?? '')
)

// ── Department ────────────────────────────────────────
const department    = ref<Department | null>(null)
const isLoading     = ref(true)
const showEditModal = ref(false)
const isSubmitting  = ref(false)
const editError     = ref('')
const editForm      = ref({ name: '', description: '' })

// ── Doctors ───────────────────────────────────────────
const doctors           = ref<any[]>([])
const isDoctorsLoading  = ref(false)
const showDoctorModal   = ref(false)
const isDoctorSubmitting = ref(false)
const doctorError       = ref('')
const searchQuery       = ref('')
const doctorForm        = ref({ name: '', email: '', password: '', specialization: '', phone: '' })
const showDeleteModal = ref(false)
const deleteError = ref('')

const currentPage = ref(1)
const lastPage = ref(1)
// Search filters only within this department's doctors
const filteredDoctors = computed(() => {
  if (!searchQuery.value.trim()) return doctors.value
  const q = searchQuery.value.toLowerCase()
  return doctors.value.filter(d =>
    d.user?.name?.toLowerCase().includes(q) ||
    d.specialization?.toLowerCase().includes(q)
  )
})

// ── Helpers ───────────────────────────────────────────
const initials = (name?: string) =>
  name ? name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) : '?'

// ── Department actions ────────────────────────────────
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
    // Мержим, не теряя вложенный branch
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
  if (doctors.value.length > 0) {
    deleteError.value = `Cannot delete: department has ${doctors.value.length} doctor(s). Remove all doctors first.`
  }
  showDeleteModal.value = true
}

const handleDelete = async () => {
  try {
    await departmentsApi.deleteDepartment(deptId)
    router.push(`/branches/${department.value?.branch_id}`)
  } catch (error: any) {
    console.error('delete error:', error)
    alert(error.response?.data?.message || error.message || 'Error deleting')
  } finally {
    showDeleteModal.value = false
  }
}
// ── Doctor actions ────────────────────────────────────
const openCreateDoctor = () => {
  doctorForm.value = { name: '', email: '', password: '', specialization: '', phone: '' }
  doctorError.value = ''
  showDoctorModal.value = true
}

const handleCreateDoctor = async () => {
  doctorError.value = ''
  if (!doctorForm.value.name.trim() || !doctorForm.value.email.trim() || !doctorForm.value.password) {
    doctorError.value = 'Name, email and password are required'
    return
  }
  if (doctorForm.value.password.length < 6) {
    doctorError.value = 'Password must be at least 6 characters'
    return
  }

  isDoctorSubmitting.value = true
  try {
    // Create user without touching current admin session
    const user = await usersApi.createUser({
      name:     doctorForm.value.name,
      email:    doctorForm.value.email,
      password: doctorForm.value.password,
      role:     'doctor',
    })

    // branch_id and department_id are taken silently from page context
    await doctorsApi.createDoctor({
      user_id:        user.id,
      branch_id:      department.value!.branch_id,
      department_id:  deptId,
      specialization: doctorForm.value.specialization || undefined,
      phone:          doctorForm.value.phone || undefined,
    })

    showDoctorModal.value = false
    await fetchDoctors()
  } catch (error: any) {
    doctorError.value =
      error.response?.data?.errors?.email?.[0] ||
      error.response?.data?.message ||
      error.response?.data?.error ||
      error.message ||
      'Error creating doctor'
  } finally {
    isDoctorSubmitting.value = false
  }
}

// ── Fetch ─────────────────────────────────────────────
const fetchDoctors = async (page = 1) => {
  isDoctorsLoading.value = true
  try {
    const response = await doctorsApi.getDoctors(department.value!.branch_id, { page })
    doctors.value = response.data?.data || response.data || []
    currentPage.value = response.data?.meta?.current_page ?? 1
    lastPage.value = response.data?.meta?.last_page ?? 1
  } catch (error) {
    console.error('❌ Error fetching doctors:', error)
    doctors.value = []
  } finally {
    isDoctorsLoading.value = false
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
  await fetchDoctors()
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

.section-header { display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-md); }
.section-header h2 { margin: 0; font-size: var(--font-xl); font-weight: 600; display: flex; align-items: center; gap: var(--space-sm); }
.count {
  display: inline-flex; align-items: center; justify-content: center;
  background: var(--color-primary); color: white;
  border-radius: 999px; font-size: 12px; font-weight: 600;
  min-width: 22px; height: 22px; padding: 0 6px;
}

/* Search */
.search-bar {
  position: relative; display: flex; align-items: center;
  background: var(--color-surface); border: 1px solid var(--color-border);
  border-radius: var(--radius-btn); padding: 0 12px; gap: var(--space-sm);
  transition: var(--transition);
}
.search-bar:focus-within { border-color: var(--color-primary); }
.search-icon { color: var(--color-text-muted); flex-shrink: 0; }
.search-input {
  flex: 1; border: none; outline: none; background: transparent;
  color: var(--color-text); font-size: 14px; font-family: var(--font-main);
  padding: 10px 0;
}
.search-input::placeholder { color: var(--color-text-muted); }
.search-clear {
  background: none; border: none; cursor: pointer;
  color: var(--color-text-muted); display: flex; align-items: center; padding: 0;
}
.search-clear:hover { color: var(--color-danger); }

/* Doctors */
.doctors-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--space-lg); }

.doctor-card {
  display: flex; gap: var(--space-md); padding: var(--space-lg);
  background: var(--color-surface); border: 1px solid var(--color-border);
  border-radius: var(--radius-card); transition: var(--transition);
}
.doctor-card:hover { border-color: var(--color-primary); box-shadow: var(--shadow-card); }

.doctor-avatar {
  width: 44px; height: 44px; border-radius: 50%; background: var(--color-primary);
  color: white; display: flex; align-items: center; justify-content: center;
  font-weight: 600; font-size: 15px; flex-shrink: 0;
}
.doctor-info { display: flex; flex-direction: column; gap: 2px; }
.doctor-info h3 { margin: 0; font-size: var(--font-base); font-weight: 600; }
.specialization { font-size: var(--font-sm); color: var(--color-primary); margin: 0; font-weight: 500; }
.phone { font-size: var(--font-xs); color: var(--color-text-muted); margin: 0; }

.empty-state { text-align: center; padding: var(--space-2xl); color: var(--color-neutral-500); }
.empty-state svg { margin-bottom: var(--space-md); opacity: 0.5; }

.not-found { text-align: center; padding: var(--space-4xl); color: var(--color-neutral-500); }
.not-found svg { margin-bottom: var(--space-md); opacity: 0.5; }

/* Modals */
.modal-form { display: flex; flex-direction: column; gap: var(--space-lg); padding: var(--space-lg) 0; }

.context-badge {
  display: flex; align-items: center; gap: var(--space-sm);
  padding: 10px 14px; background: var(--color-bg);
  border: 1px solid var(--color-border); border-radius: var(--radius-btn);
  font-size: var(--font-sm); color: var(--color-text-muted); font-weight: 500;
}
.sep { color: var(--color-border); }

.modal-actions { display: flex; gap: var(--space-md); justify-content: flex-end; }

.error-box {
  background: #fee2e2; border: 1px solid #fecaca;
  border-radius: var(--radius-btn); padding: var(--space-md);
  font-size: var(--font-sm); color: #991b1b;
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