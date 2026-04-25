<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>Medicard</h1>
        <p>Medical Management Platform</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <BaseInput
          v-model="form.email"
          type="email"
          label="Email"
          placeholder="your@email.com"
          required
          @blur="validateEmail"
        />
        <div v-if="errors.email" class="error-message">{{ errors.email }}</div>

        <BaseInput
          v-model="form.password"
          type="password"
          label="Password"
          placeholder="••••••••"
          required
        />
        <div v-if="errors.password" class="error-message">{{ errors.password }}</div>

        <div v-if="errors.general" class="error-box">{{ errors.general }}</div>

        <BaseButton
          type="submit"
          variant="primary"
          size="lg"
          :loading="isLoading"
          :disabled="isLoading"
          class="w-full"
        >
          {{ isLoading ? 'Signing in...' : 'Sign In' }}
        </BaseButton>
      </form>

      <div class="login-footer">
        <p>Don't have an account? <router-link to="/register">Sign up</router-link></p>
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
import { nextTick } from 'vue'
const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const errors = reactive({
  email: '',
  password: '',
  general: '',
})

const isLoading = ref(false)

const validateEmail = (): boolean => {
  if (!form.email) {
    errors.email = 'Email is required'
    return false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Invalid email format'
    return false
  }
  errors.email = ''
  return true
}

const validatePassword = (): boolean => {
  if (!form.password) {
    errors.password = 'Password is required'
    return false
  } else if (form.password.length < 6) {
    errors.password = 'Password must be at least 6 characters'
    return false
  }
  errors.password = ''
  return true
}

const handleLogin = async () => {
  await nextTick()
  
  console.log('📋 Form values:', { email: form.email, password: form.password })

  const isEmailValid = validateEmail()
  const isPasswordValid = validatePassword()

  console.log('✅ Validation:', { isEmailValid, isPasswordValid })

  if (!isEmailValid || !isPasswordValid) {
    console.warn('❌ Validation failed, stopping')
    return
  }

  errors.general = ''
  isLoading.value = true

  try {
    console.log('🚀 Sending request to API...')
    const result = await authStore.login(form.email, form.password)
    console.log('✅ Login success:', result)
    router.push('/dashboard')
  } catch (error: any) {
    console.error('❌ Login error full object:', error)
    console.error('❌ error.response:', error.response)
    console.error('❌ error.response?.data:', error.response?.data)
    console.error('❌ error.message:', error.message)

    errors.general =
      error.response?.data?.error ||
      error.response?.data?.message ||
      error.message ||
      'Login failed. Please try again.'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-info) 100%);
  padding: var(--space-md);
}

.login-card {
  background: var(--color-white);
  border-radius: var(--radius-card);
  box-shadow: var(--shadow-modal);
  width: 100%;
  max-width: 420px;
  padding: var(--space-2xl);
}

.login-header {
  text-align: center;
  margin-bottom: var(--space-2xl);
}

.login-header h1 {
  font-size: var(--font-2xl);
  font-weight: 600;
  color: var(--color-primary);
  margin: 0 0 var(--space-xs) 0;
}

.login-header p {
  font-size: var(--font-sm);
  color: var(--color-neutral-500);
  margin: 0;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
  margin-bottom: var(--space-xl);
}

.error-message {
  font-size: var(--font-xs);
  color: var(--color-danger);
  margin-top: var(--space-xs) * -0.5;
  margin-bottom: var(--space-xs);
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

.login-footer {
  text-align: center;
  font-size: var(--font-sm);
  color: var(--color-neutral-600);
}

.login-footer a {
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 500;
  transition: color var(--transition-fast);
}

.login-footer a:hover {
  color: var(--color-info);
  text-decoration: underline;
}
</style>
