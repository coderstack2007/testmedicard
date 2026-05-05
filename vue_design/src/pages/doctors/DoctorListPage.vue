<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Doctors</h1>
      <BaseButton variant="primary" @click="openCreate">
        <Plus :size="18" />
        Add Doctor
      </BaseButton>
    </div>

    <!-- Статичная хлебная крошка -->
    <div class="branch-breadcrumb" v-if="department">
      <span>🏥 {{ department.branch?.name }}</span>
      <span class="sep">›</span>
      <span>🏬 {{ department.name }}</span>
    </div>

    <div class="filters">
      <div class="search-bar">
        <Search :size="16" class="search-icon" />
        <input v-model="searchQuery" type="text" class="search-input" placeholder="Search by name..." />
        <button v-if="searchQuery" class="search-clear" @click="searchQuery = ''">
          <X :size="14" />
        </button>
      </div>
      <select v-model="selectedSpecialization" class="filter-select">
        <option value="">All Specializations</option>
        <option v-for="s in specializationsList" :key="s" :value="s">{{ s }}</option>
      </select>
    </div>

    <LoadingSpinner v-if="isLoading" message="Loading doctors..." />
    <template v-else>
      <div v-if="filteredDoctors.length > 0" class="doctors-grid">
        <div
          v-for="doctor in filteredDoctors"
          :key="doctor.id"
          class="doctor-card"
          @click="router.push(`/doctors/${doctor.id}`)"
        >
          <div class="doctor-avatar">{{ initials(doctor.user?.name) }}</div>
          <div class="doctor-info">
            <h3>{{ doctor.user?.name }}</h3>
            <p class="specialization">{{ doctor.specialization || 'No specialization' }}</p>
            <p class="meta">{{ doctor.department?.name }} · {{ doctor.branch?.name }}</p>
            <p class="phone" v-if="doctor.phone">📞 {{ doctor.phone }}</p>
          </div>
        </div>
      </div>
      <div v-else class="empty-state">
        <Stethoscope :size="48" />
        <p>No doctors found</p>
      </div>

      <div v-if="lastPage > 1" class="pagination">
        <BaseButton variant="ghost" :disabled="currentPage === 1" @click="fetchDoctors(currentPage - 1)">← Prev</BaseButton>
        <button
          v-for="page in lastPage"
          :key="page"
          class="page-btn"
          :class="{ active: page === currentPage }"
          @click="fetchDoctors(page)"
        >{{ page }}</button>
        <BaseButton variant="ghost" :disabled="currentPage === lastPage" @click="fetchDoctors(currentPage + 1)">Next →</BaseButton>
      </div>
    </template>
  </div>

  <BaseModal v-if="showModal" :open="showModal" @close="closeModal" title="Add New Doctor">
    <form @submit.prevent="handleSubmit" class="modal-form">
      <BaseInput v-model="formData.name" label="Full Name" placeholder="Dr. John Smith" required />
      <BaseInput v-model="formData.email" type="email" label="Email" placeholder="doctor@medicard.uz" required />
      <BaseInput v-model="formData.password" type="password" label="Password" placeholder="••••••••" required />

      <div class="form-group">
        <label class="form-label">Branch & Department</label>
        <div class="static-location">
          <span>🏥 {{ department?.branch?.name }}</span>

        </div>
      </div>
      <div class="form-group">

        <div class="static-location">


          <span>🏬 {{ department?.name }}</span>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Specialization</label>
        <select v-model="formData.specialization" class="form-select">
          <option value="">Select specialization</option>
          <option v-for="s in specializationsList" :key="s" :value="s">{{ s }}</option>
        </select>
      </div>

      <BaseInput v-model="formData.phone" label="Phone" placeholder="+998901234567" />

      <div v-if="formError" class="error-box">{{ formError }}</div>
      <div class="modal-actions">
        <BaseButton type="button" variant="ghost" @click="closeModal">Cancel</BaseButton>
        <BaseButton type="submit" variant="primary" :loading="isSubmitting">Create Doctor</BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import * as doctorsApi from '@/api/doctors'
import * as departmentsApi from '@/api/departments'
import * as authApi from '@/api/auth'
import * as specializationsApi from '@/api/specializations'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { Plus, Stethoscope, Search, X } from 'lucide-vue-next'
import * as specializationsApi from '@/api/specializations'

const specializationsList = ref<string[]>([])

onMounted(async () => {
  try {
    const [doctorData, specsData] = await Promise.all([
      doctorsApi.getDoctor(doctorId),
      specializationsApi.getSpecializations()
    ])
    doctor.value = doctorData
    specializationsList.value = specsData.data // ← .data так как в getSpecializations нет .data
  } catch (error) {
    console.error('❌ Error fetching:', error)
  } finally {
    isLoading.value = false
  }
})
const router = useRouter()
const route  = useRoute()

const departmentId = Number(route.params.id)

// ── State ─────────────────────────────────────────────
const doctors             = ref<any[]>([])
const department          = ref<any>(null)
const specializationsList = ref<string[]>([])
const selectedSpecialization = ref('')
const searchQuery            = ref('')
const currentPage  = ref(1)
const lastPage     = ref(1)
const isLoading    = ref(false)
const showModal    = ref(false)
const isSubmitting = ref(false)
const formError    = ref('')

const formData = ref({
  name: '', email: '', password: '', specialization: '', phone: '',
})

// ── Computed ──────────────────────────────────────────
const filteredDoctors = computed(() => {
  let list = doctors.value
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(d => d.user?.name?.toLowerCase().includes(q))
  }
  if (selectedSpecialization.value) {
    list = list.filter(d => d.specialization === selectedSpecialization.value)
  }
  return list
})

// ── Helpers ───────────────────────────────────────────
const initials = (name?: string) =>
  name ? name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) : '?'

// ── Actions ───────────────────────────────────────────
const openCreate = () => {
  formError.value = ''
  formData.value = { name: '', email: '', password: '', specialization: '', phone: '' }
  showModal.value = true
}
const closeModal = () => { showModal.value = false }

const handleSubmit = async () => {
  formError.value = ''
  if (!formData.value.name.trim() || !formData.value.email.trim() || !formData.value.password) {
    formError.value = 'Name, email and password are required'; return
  }
  if (formData.value.password.length < 6) {
    formError.value = 'Password must be at least 6 characters'; return
  }

  isSubmitting.value = true
  try {
    const userResponse = await authApi.register({
      name: formData.value.name,
      email: formData.value.email,
      password: formData.value.password,
      password_confirmation: formData.value.password,
      role: 'doctor',
    })

    const userId = userResponse?.user?.id ?? userResponse?.id

    await doctorsApi.createDoctor({
      user_id:        userId,
      branch_id:      department.value?.branch?.id,
      department_id:  departmentId,
      specialization: formData.value.specialization || undefined,
      phone:          formData.value.phone || undefined,
    })

    closeModal()
    await fetchDoctors()
  } catch (error: any) {
    formError.value =
      error.response?.data?.errors?.email?.[0] ||
      error.response?.data?.message ||
      error.message || 'Error creating doctor'
  } finally {
    isSubmitting.value = false
  }
}

// ── Fetch ─────────────────────────────────────────────
const fetchDepartment = async () => {
  try {
    // getDepartment уже возвращает data.data напрямую
    department.value = await departmentsApi.getDepartment(departmentId)
    console.log('✅ department loaded:', department.value)
  } catch (e) {
    console.error('Failed to load department', e)
  }
}

const fetchDoctors = async (page = 1) => {
  const branchId = department.value?.branch_id ?? department.value?.branch?.id
  if (!branchId) {
    console.warn('No branchId available yet')
    return
  }
  isLoading.value = true
  try {
    const response = await doctorsApi.getDoctors(branchId, { page })
    doctors.value     = response.data?.data || response.data || []
    currentPage.value = response.data?.meta?.current_page ?? 1
    lastPage.value    = response.data?.meta?.last_page ?? 1
  } catch (error) {
    console.error('❌ Error fetching doctors:', error)
    doctors.value = []
  } finally {
    isLoading.value = false
  }
}

const fetchSpecializations = async () => {
  try {
    const response = await specializationsApi.getSpecializations()
    specializationsList.value = (response.data || []).map((s: any) => s.name)
  } catch (e) {
    console.error('Failed to load specializations', e)
  }
}

onMounted(async () => {
  await fetchDepartment()  // ждём — нам нужен branch_id
  await fetchDoctors()
  fetchSpecializations()
  
})

</script>

<style scoped>
.page-container { display: flex; flex-direction: column; gap: var(--space-xl); }
.page-header { display: flex; justify-content: space-between; align-items: center; }
.page-header h1 { font-size: var(--font-2xl); font-weight: 600; margin: 0; }

.branch-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: var(--font-sm);
  color: var(--color-text-muted);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-btn);
  padding: 8px 14px;
  width: fit-content;
}
.branch-breadcrumb .sep { color: var(--color-neutral-400); }

.filters { display: flex; gap: var(--space-md); align-items: center; flex-wrap: wrap; }
.filter-select {
  padding: 8px 12px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-btn);
  background: var(--color-surface);
  color: var(--color-text);
  font-size: 14px;
  font-family: var(--font-main);
  cursor: pointer;
  min-width: 180px;
}
.filter-select:focus { outline: none; border-color: var(--color-primary); }

.search-bar {
  display: flex; align-items: center; flex: 1; min-width: 200px;
  background: var(--color-surface); border: 1px solid var(--color-border);
  border-radius: var(--radius-btn); padding: 0 12px; gap: var(--space-sm); transition: var(--transition);
}
.search-bar:focus-within { border-color: var(--color-primary); }
.search-icon { color: var(--color-text-muted); flex-shrink: 0; }
.search-input {
  flex: 1; border: none; outline: none; background: transparent;
  color: var(--color-text); font-size: 14px; font-family: var(--font-main); padding: 8px 0;
}
.search-input::placeholder { color: var(--color-text-muted); }
.search-clear { background: none; border: none; cursor: pointer; color: var(--color-text-muted); display: flex; align-items: center; padding: 0; }
.search-clear:hover { color: var(--color-danger); }

.doctors-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--space-lg); }
.doctor-card {
  display: flex; gap: var(--space-md); padding: var(--space-lg);
  background: var(--color-surface); border: 1px solid var(--color-border);
  border-radius: var(--radius-card); transition: var(--transition); cursor: pointer;
}
.doctor-card:hover { border-color: var(--color-primary); box-shadow: var(--shadow-card); }
.doctor-avatar {
  width: 48px; height: 48px; border-radius: 50%; background: var(--color-primary);
  color: white; display: flex; align-items: center; justify-content: center;
  font-weight: 600; font-size: 16px; flex-shrink: 0;
}
.doctor-info { display: flex; flex-direction: column; gap: 2px; }
.doctor-info h3 { margin: 0; font-size: var(--font-base); font-weight: 600; }
.specialization { font-size: var(--font-sm); color: var(--color-primary); margin: 0; font-weight: 500; }
.meta { font-size: var(--font-xs); color: var(--color-text-muted); margin: 0; }
.phone { font-size: var(--font-xs); color: var(--color-text-muted); margin: 0; }

.empty-state { text-align: center; padding: var(--space-4xl) var(--space-2xl); color: var(--color-neutral-500); }
.empty-state svg { margin-bottom: var(--space-md); opacity: 0.5; }

.pagination { display: flex; align-items: center; justify-content: center; gap: var(--space-sm); margin-top: var(--space-lg); }
.page-btn {
  width: 36px; height: 36px; border-radius: var(--radius-btn); border: 1px solid var(--color-border);
  background: var(--color-surface); cursor: pointer; font-size: var(--font-sm);
  color: var(--color-neutral-900); transition: var(--transition);
}
.page-btn.active { background: var(--color-primary); color: white; border-color: var(--color-primary); }
.page-btn:hover:not(.active) { border-color: var(--color-primary); }

.modal-form { display: flex; flex-direction: column; gap: var(--space-lg); padding: var(--space-lg) 0; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-label { font-size: 14px; font-weight: 500; color: var(--color-text); }
.form-select {
  padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-btn);
  background: var(--color-surface); color: var(--color-text); font-size: 14px; font-family: var(--font-main); cursor: pointer;
}
.form-select:focus { outline: none; border-color: var(--color-primary); }

.static-location {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 12px; background: var(--color-bg);
  border: 1px solid var(--color-border); border-radius: var(--radius-btn);
  font-size: 14px; color: var(--color-text-muted);
}
.static-location .sep { color: var(--color-neutral-400); }

.modal-actions { display: flex; gap: var(--space-md); justify-content: flex-end; margin-top: var(--space-lg); }
.error-box {
  background: #fee2e2; border: 1px solid #fecaca;
  border-radius: var(--radius-btn); padding: var(--space-md);
  font-size: var(--font-sm); color: #991b1b;
}
</style>