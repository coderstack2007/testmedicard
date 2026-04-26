import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import type { Branch } from '@/types'
import * as branchesApi from '@/api/branches'
import { useAuthStore } from './auth'
import client from '@/api/client'

export const useBranchStore = defineStore('branch', () => {
  const branches = ref<Branch[]>([])
  const currentBranch = ref<Branch | null>(null)
  const isLoading = ref(false)

  async function fetchBranches() {
    isLoading.value = true
    try {
      const response = await branchesApi.getBranches()
      branches.value = response.data || []
    } finally {
      isLoading.value = false
    }
  }

  async function fetchBranch(id: number) {
    isLoading.value = true
    try {
      currentBranch.value = await branchesApi.getBranch(id)
    } finally {
      isLoading.value = false
    }
  }
  async function deleteBranch(id: number) {
  isLoading.value = true
  try {
    await branchesApi.deleteBranch(id)
    branches.value = branches.value.filter(b => b.id !== id)
  } finally {
    isLoading.value = false
  }
}
  async function createBranch(data: Partial<Branch>) {
    isLoading.value = true
    try {
      const branch = await branchesApi.createBranch(data)
      branches.value.push(branch.data!)
      return branch.data
    } finally {
      isLoading.value = false
    }
  }

  async function updateBranch(id: number, data: Partial<Branch>) {
    isLoading.value = true
    try {
      const branch = await branchesApi.updateBranch(id, data)
      const index = branches.value.findIndex(b => b.id === id)
      if (index !== -1) {
        branches.value[index] = branch.data!
      }
      return branch.data
    } finally {
      isLoading.value = false
    }
  }
const auth = useAuthStore()
const canManage = computed(() =>
  ['moderator', 'absolute_admin'].includes(auth.user?.role ?? '')
)
  return {
    branches,
    currentBranch,
    isLoading,
    fetchBranches,
    fetchBranch,
    createBranch,
    updateBranch,
    deleteBranch,
    canManage,
  }
})
