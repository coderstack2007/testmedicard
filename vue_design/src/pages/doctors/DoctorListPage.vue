<template>
  <AppLayout>
    <div class="page-container">
      <div class="page-header">
        <h1>Doctors</h1>
        <BaseButton variant="primary" @click="openCreate">
          <Plus :size="18" />
          Add Doctor
        </BaseButton>
      </div>

      <!-- Filter by branch -->
      <div class="filters">
        <select v-model="selectedBranchId" class="filter-select" @change="onBranchChange">
          <option :value="null">All Branches</option>
          <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
        </select>
      </div>

      <LoadingSpinner v-if="isLoading" message="Loading doctors..." />

      <div v-else-if="doctors.length > 0" class="doctors-grid">
        <div v-for="doctor in doctors" :key="doctor.id" class="doctor-card">
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
        <span v-if="selectedBranchId">Try selecting a different branch</span>
      </div>
    </div>

    <!-- Create Doctor Modal -->
    <BaseModal v-if="showModal" :open="showModal" @close="closeModal" title="Add New Doctor">
      <form @submit.prevent="handleSubmit" class="modal-form">

        <!-- Step indicator -->
        <div class="steps">
          <div :class="['step', { active: step === 1, done: step > 1 }]">
            <span class="step-num">1</span> Account
          </div>
          <div class="step-line" />
          <div :class="['step', { active: step === 2 }]">
            <span class="step-num">2</span> Profile
          </div>
        </div>

        <!-- Step 1: Create user account -->
        <template v-if="step === 1">
          <BaseInput v-model="formData.name" label="Full Name" placeholder="Dr. John Smith" required />
          <BaseInput v-model="formData.email" type="email" label="Email" placeholder="doctor@medicard.uz" required />
          <BaseInput v-model="formData.password" type="password" label="Password" placeholder="••••••••" required />
          <div v-if="stepError" class="error-box">{{ stepError }}</div>
          <div class="modal-actions">
            <BaseButton type="button" variant="ghost" @click="closeModal">Cancel</BaseButton>
            <BaseButton type="button" variant="primary" :loading="isSubmitting" @click="goToStep2">
              Next →
            </BaseButton>
          </div>
        </template>

        <!-- Step 2: Doctor profile -->
        <template v-if="step === 2">
          <div class="form-group">
            <label class="form-label">Branch <span class="required">*</span></label>
            <select v-model="formData.branch_id" class="form-select" required @change="onFormBranchChange">
              <option value="">Select branch</option>
              <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Department <span class="required">*</span></label>
            <select v-model="formData.department_id" class="form-select" required :disabled="!formData.branch_id">
              <option value="">{{ formData.branch_id ? 'Select department' : 'Select branch first' }}</option>
              <option v-for="d in filteredDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
            </select>
          </div>

          <BaseInput v-model="formData.specialization" label="Specialization" placeholder="Cardiology" />
          <BaseInput v-model="formData.phone" label="Phone" placeholder="+998901234567" />

          <div v-if="stepError" class="error-box">{{ stepError }}</div>
          <div class="modal-actions">
            <BaseButton type="button" variant="ghost" @click="step = 1">← Back</BaseButton>
            <BaseButton type="submit" variant="primary" :loading="isSubmitting">Create Doctor</BaseButton>
          </div>
        </template>

      </form>
    </BaseModal>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import * as doctorsApi from '@/api/doctors'
import * as branchesApi from '@/api/branches'
import * as departmentsApi from '@/api/departments'
import * as authApi from '@/api/auth'

import AppLayout from '@/components/layout/AppLayout.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { Plus, Stethoscope } from 'lucide-vue-next'
import type { Branch, Department } from '@/types'

// ── State ────────────────────────────────────────────
const doctors = ref<any[]>([])
const branches = ref<Branch[]>([])
const allDepartments = ref<Department[]>([])
const selectedBranchId = ref<number | null>(null)

const isLoading = ref(false)
const showModal = ref(false)
const isSubmitting = ref(false)
const step = ref(1)
const stepError = ref('')
const createdUserId = ref<number | null>(null)

const formData = ref({
  // Step 1
  name: '',
  email: '',
  password: '',
  // Step 2
  branch_id: '' as number | '',
  department_id: '' as number | '',
  specialization: '',
  phone: '',
})

// ── Computed ─────────────────────────────────────────
const filteredDepartments = computed(() =>
  formData.value.branch_id
    ? allDepartments.value.filter(d => d.branch_id === formData.value.branch_id)
    : []
)

// ── Helpers ───────────────────────────────────────────
const initials = (name?: string) =>
  name ? name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) : '?'

// ── Actions ───────────────────────────────────────────
const openCreate = () => {
  step.value = 1
  stepError.value = ''
  createdUserId.value = null
  formData.value = { name: '', email: '', password: '', branch_id: '', department_id: '', specialization: '', phone: '' }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const onFormBranchChange = () => {
  formData.value.department_id = ''
  if (formData.value.branch_id) {
    loadDepartmentsForBranch(formData.value.branch_id as number)
  }
}

const loadDepartmentsForBranch = async (branchId: number) => {
  try {
    const response = await departmentsApi.getDepartments(branchId)
    const depts: Department[] = response.data?.data || response.data || []
    // merge into allDepartments without duplicates
    depts.forEach(d => {
      if (!allDepartments.value.find(x => x.id === d.id)) {
        allDepartments.value.push(d)
      }
    })
  } catch (e) {
    console.error('Failed to load departments', e)
  }
}

const goToStep2 = async () => {
  stepError.value = ''
  if (!formData.value.name.trim() || !formData.value.email.trim() || !formData.value.password) {
    stepError.value = 'All fields are required'
    return
  }
  if (formData.value.password.length < 6) {
    stepError.value = 'Password must be at least 6 characters'
    return
  }

  isSubmitting.value = true
  try {
    // Register new user with doctor role
    const response = await authApi.register({
      name: formData.value.name,
      email: formData.value.email,
      password: formData.value.password,
      password_confirmation: formData.value.password,
      role: 'doctor',
    })
    console.log('✅ User created:', response)
    createdUserId.value = response?.user?.id ?? response?.id
    step.value = 2
  } catch (error: any) {
    console.error('❌ Register error:', error.response?.data)
    stepError.value =
      error.response?.data?.message ||
      error.response?.data?.errors?.email?.[0] ||
      error.message ||
      'Error creating user account'
  } finally {
    isSubmitting.value = false
  }
}

const handleSubmit = async () => {
  stepError.value = ''
  if (!formData.value.branch_id || !formData.value.department_id) {
    stepError.value = 'Branch and Department are required'
    return
  }
  if (!createdUserId.value) {
    stepError.value = 'User account not created'
    return
  }

  isSubmitting.value = true
  try {
    const payload = {
      user_id: createdUserId.value,
      branch_id: formData.value.branch_id,
      department_id: formData.value.department_id,
      specialization: formData.value.specialization || undefined,
      phone: formData.value.phone || undefined,
    }
    console.log('🚀 Creating doctor profile:', payload)
    const result = await doctorsApi.createDoctor(payload)
    console.log('✅ Doctor created:', result)
    closeModal()
    await fetchDoctors()
  } catch (error: any) {
    console.error('❌ Doctor create error:', error.response?.data)
    stepError.value =
      error.response?.data?.message ||
      error.response?.data?.error ||
      error.message ||
      'Error creating doctor profile'
  } finally {
    isSubmitting.value = false
  }
}

const onBranchChange = () => {
  fetchDoctors()
}

const fetchDoctors = async () => {
  if (!selectedBranchId.value) {
    doctors.value = []
    return
  }
  isLoading.value = true
  try {
    const response = await doctorsApi.getDoctors(selectedBranchId.value)
    doctors.value = response.data?.data || response.data || []
  } catch (error) {
    console.error('❌ Error fetching doctors:', error)
    doctors.value = []
  } finally {
    isLoading.value = false
  }
}

const fetchBranches = async () => {
  try {
    const response = await branchesApi.getBranches()
    branches.value = response.data?.data || response.data || []
    // auto-select first branch
    if (branches.value.length > 0) {
      selectedBranchId.value = branches.value[0].id
      await fetchDoctors()
    }
  } catch (error) {
    console.error('❌ Error fetching branches:', error)
  }
}

onMounted(() => {
  fetchBranches()
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

.filters {
  display: flex;
  gap: var(--space-md);
}

.filter-select,
.form-select {
  padding: 8px 12px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-btn);
  background: var(--color-surface);
  color: var(--color-text);
  font-size: 14px;
  font-family: var(--font-main);
  cursor: pointer;
  min-width: 200px;
}

.filter-select:focus,
.form-select:focus {
  outline: none;
  border-color: var(--color-primary);
}

.form-select:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.doctors-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: var(--space-lg);
}

.doctor-card {
  display: flex;
  gap: var(--space-md);
  padding: var(--space-lg);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-card);
  transition: var(--transition);
}

.doctor-card:hover {
  border-color: var(--color-primary);
  box-shadow: var(--shadow-card);
}

.doctor-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--color-primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 16px;
  flex-shrink: 0;
}

.doctor-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.doctor-info h3 {
  margin: 0;
  font-size: var(--font-base);
  font-weight: 600;
}

.specialization {
  font-size: var(--font-sm);
  color: var(--color-primary);
  margin: 0;
  font-weight: 500;
}

.meta {
  font-size: var(--font-xs);
  color: var(--color-text-muted);
  margin: 0;
}

.phone {
  font-size: var(--font-xs);
  color: var(--color-text-muted);
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

/* Modal */
.modal-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
  padding: var(--space-lg) 0;
}

.steps {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-bottom: var(--space-md);
}

.step {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  font-size: var(--font-sm);
  color: var(--color-text-muted);
  font-weight: 500;
}

.step.active {
  color: var(--color-primary);
}

.step.done {
  color: var(--color-success);
}

.step-num {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--color-border);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 600;
}

.step.active .step-num {
  background: var(--color-primary);
  color: white;
}

.step.done .step-num {
  background: var(--color-success);
  color: white;
}

.step-line {
  flex: 1;
  height: 1px;
  background: var(--color-border);
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 14px;
  font-weight: 500;
  color: var(--color-text);
}

.required {
  color: var(--color-danger);
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