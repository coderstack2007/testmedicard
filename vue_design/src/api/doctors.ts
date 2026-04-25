import client from './client'
import type { User, DoctorProfile, PaginatedResponse } from '@/types'

export const getDoctors = (branchId: number, params?: Record<string, any>) =>
  client.get<PaginatedResponse<User>>(`/branches/${branchId}/doctors`, { params })

export const createDoctor = (data: Partial<DoctorProfile>) =>
  client.post<{ data: DoctorProfile }>('/doctors', data)

export const updateDoctor = (id: number, data: Partial<DoctorProfile>) =>
  client.put<{ data: DoctorProfile }>(`/doctors/${id}`, data)

export const deleteDoctor = (id: number) =>
  client.delete(`/doctors/${id}`)
