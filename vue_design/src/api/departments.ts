import client from './client'
import type { Department } from '@/types'

export const getDepartments = async (branchId: number, params?: { page?: number }) => {
  const response = await client.get(`/branches/${branchId}/departments`, { params })
  console.log('📡 getDepartments raw:', response.data)
  return response
}

export const getDepartment = async (id: number) => {
  const response = await client.get<{ data: Department }>(`/departments/${id}`)
  console.log('📡 getDepartment raw:', response.data)
  return response.data.data ?? response.data
}

export const createDepartment = async (branchId: number, data: { name: string; description?: string }) => {
  console.log('📡 createDepartment payload:', { branchId, data })
  const response = await client.post(`/branches/${branchId}/departments`, data)
  console.log('📡 createDepartment raw:', response.data)
  return response.data
}

export const updateDepartment = async (id: number, data: { name?: string; description?: string }) => {
  console.log('📡 updateDepartment payload:', { id, data })
  const response = await client.put(`/departments/${id}`, data)
  console.log('📡 updateDepartment raw:', response.data)
  return response.data
}

export const deleteDepartment = async (id: number) => {
  console.log('📡 deleteDepartment id:', id)
  await client.delete(`/departments/${id}`)
}