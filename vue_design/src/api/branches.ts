import client from './client'
import type { Branch, PaginatedResponse } from '@/types'

export const getBranches = async (params?: Record<string, any>) => {
  const response = await client.get<PaginatedResponse<Branch>>('/branches', { params })
  return response
}

export const getBranch = async (id: number) => {
  const response = await client.get<Branch>(`/branches/${id}`)
  return response
}

export const createBranch = async (data: Partial<Branch>) => {
  const response = await client.post<{ data: Branch }>('/branches', data)
  return response
}

export const updateBranch = async (id: number, data: Partial<Branch>) => {
  const response = await client.put<{ data: Branch }>(`/branches/${id}`, data)
  return response
}

// branches.ts
export const deleteBranch = async (id: number) => {
  await client.delete(`/branches/${id}`)
}
