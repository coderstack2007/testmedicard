<template>
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h1>Create Account</h1>
        <p>Join Medicard Medical Platform</p>
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <BaseInput
          v-model="form.name"
          type="text"
          label="Full Name"
          placeholder="John Doe"
          required
          @blur="errors.name = ''"
        />
        <div v-if="errors.name" class="error-message">{{ errors.name }}</div>

        <BaseInput
          v-model="form.email"
          type="email"
          label="Email"
          placeholder="your@email.com"
          required
          @blur="validateEmail"
        />
        <div v-if="errors.email" class="error-message">{{ errors.email }}</div>

        <div class="form-group">
          <label class="form-label">Role</label>
          <select v-model="form.role" class="form-select" required>
            <option value="">Select your role</option>
            <option value="patient">Patient</option>
            <option value="doctor">Doctor</option>
          </select>
          <div v-if="errors.role" class="error-message">{{ errors.role }}</div>
        </div>

        <BaseInput
          v-model="form.password"
          type="password"
          label="Password"
          placeholder="••••••••"
          hint="Minimum 8 characters"
          required
          @blur="errors.password = ''"
        />
        <div v-if="errors.password" class="error-message">{{ errors.password }}</div>

        <BaseInput
          v-model="form.passwordConfirmation"
          type="password"
          label="Confirm Password"
          placeholder="••••••••"
          required
          @blur="errors.passwordConfirmation = ''"
        />
        <div v-if="errors.passwordConfirmation" class="error-message">
          {{ errors.passwordConfirmation }}
        </div>

        <div v-if="errors.general" class="error-box">{{ errors.general }}</div>

        <BaseButton
          type="submit"
          variant="primary"
          size="lg"
          :loading="isLoading"
          :disabled="isLoading"
          class="w-full"
        >
          {{ isLoading ? 'Creating Account...' : 'Create Account' }}
        </BaseButton>
      </form>

      <div class="register-footer">
        <p>Already have an account? <router-link to="/login">Sign in</router-link></p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  role: '',
  password: '',
  passwordConfirmation: '',
})

const errors = reactive({
  name: '',
  email: '',
  role: '',
  password: '',
  passwordConfirmation: '',
  general: '',
})

const isLoading = ref(false)

const validateEmail = () => {
  if (!form.email) {
    errors.email = 'Email is required'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Invalid email format'
  } else {
    errors.email = ''
  }
}

const validateForm = (): boolean => {
  let isValid = true

  if (!form.name.trim()) {
    errors.name = 'Name is required'
    isValid = false
  }

  validateEmail()
  if (errors.email) isValid = false

  if (!form.role) {
    errors.role = 'Please select a role'
    isValid = false
  }

  if (!form.password) {
    errors.password = 'Password is required'
    isValid = false
  } else if (form.password.length < 8) {
    errors.password = 'Password must be at least 8 characters'
    isValid = false
  }

  if (!form.passwordConfirmation) {
    errors.passwordConfirmation = 'Please confirm your password'
    isValid = false
  } else if (form.password !== form.passwordConfirmation) {
    errors.passwordConfirmation = 'Passwords do not match'
    isValid = false
  }

  return isValid
}

const handleRegister = async () => {
  errors.general = ''

  if (!validateForm()) {
    return
  }

  isLoading.value = true

  try {
    await authStore.register(
      form.name,
      form.email,
      form.password,
      form.role as 'patient' | 'doctor'
    )
    router.push('/dashboard')
  } catch (error: any) {
    errors.general =
      error.response?.data?.message || error.message 
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.register-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-info) 100%);
  padding: var(--space-md);
}

.register-card {
  background: var(--color-white);
  border-radius: var(--radius-card);
  box-shadow: var(--shadow-modal);
  width: 100%;
  max-width: 480px;
  padding: var(--space-2xl);
}

.register-header {
  text-align: center;
  margin-bottom: var(--space-2xl);
}

.register-header h1 {
  font-size: var(--font-2xl);
  font-weight: 600;
  color: var(--color-primary);
  margin: 0 0 var(--space-xs) 0;
}

.register-header p {
  font-size: var(--font-sm);
  color: var(--color-neutral-500);
  margin: 0;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
  margin-bottom: var(--space-xl);
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-xs);
}

.form-label {
  font-size: var(--font-sm);
  font-weight: 500;
  color: var(--color-neutral-700);
}

.form-select {
  padding: var(--space-md);
  font-size: var(--font-base);
  border: 1px solid var(--color-neutral-200);
  border-radius: var(--radius-button);
  background: var(--color-white);
  color: var(--color-neutral-900);
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
  font-family: 'DM Sans', sans-serif;
}

.form-select:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.error-message {
  font-size: var(--font-xs);
  color: var(--color-danger);
  margin-top: var(--space-xs) * -0.5;
}

.error-box {
  background: #fee2e2;
  border: 1px solid #fecaca;
  border-radius: var(--radius-button);
  padding: var(--space-md);
  font-size: var(--font-sm);
  color: #991b1b;
}

.w-full {
  width: 100%;
}

.register-footer {
  text-align: center;
  font-size: var(--font-sm);
  color: var(--color-neutral-600);
}

.register-footer a {
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 500;
  transition: color var(--transition-fast);
}

.register-footer a:hover {
  color: var(--color-info);
  text-decoration: underline;
}
</style>
