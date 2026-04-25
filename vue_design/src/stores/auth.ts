import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User, UserRole } from '@/types'
import * as authApi from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('medicard_token'))

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'absolute_admin')
  const isModerator = computed(() => ['moderator', 'absolute_admin'].includes(user.value?.role as string))
  const isDoctor = computed(() => user.value?.role === 'doctor')
  const isPatient = computed(() => user.value?.role === 'patient')

  async function login(email: string, password: string) {
    const response = await authApi.login({ email, password })
    token.value = response.token
    user.value = response.user
    localStorage.setItem('medicard_token', response.token)
    localStorage.setItem('medicard_user', JSON.stringify(response.user))
  }

  async function register(name: string, email: string, password: string, role: 'patient' | 'doctor') {
    const response = await authApi.register({
      name,
      email,
      password,
      password_confirmation: password,
      role,
    })
    token.value = response.token
    user.value = response.user
    localStorage.setItem('medicard_token', response.token)
    localStorage.setItem('medicard_user', JSON.stringify(response.user))
  }

  async function fetchMe() {
    if (!token.value) return
    user.value = await authApi.me()
  }

  async function logout() {
    try {
      await authApi.logout()
    } catch (error) {
      // Continue logout even if API call fails
    }
    user.value = null
    token.value = null
    localStorage.removeItem('medicard_token')
    localStorage.removeItem('medicard_user')
  }

  function loadFromStorage() {
    const storedUser = localStorage.getItem('medicard_user')
    const storedToken = localStorage.getItem('medicard_token')
    
    if (storedUser && storedToken) {
      user.value = JSON.parse(storedUser)
      token.value = storedToken
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    isModerator,
    isDoctor,
    isPatient,
    login,
    register,
    fetchMe,
    logout,
    loadFromStorage,
  }
})
