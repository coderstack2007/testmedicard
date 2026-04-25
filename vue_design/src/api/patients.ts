import client from './client'
import type { User, PatientProfile, PaginatedResponse } from '@/types'

export const getPatients = (params?: Record<string, any>) =>
  client.get<PaginatedResponse<User>>('/patients', { params })

export const getPatient = (id: number) =>
  client.get<User>(`/patients/${id}`)

export const createPatient = (data: Partial<PatientProfile>) =>
  client.post<{ data: PatientProfile }>('/patients', data)

export const deletePatient = (id: number) =>
  client.delete(`/patients/${id}`)
