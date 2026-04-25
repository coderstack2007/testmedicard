import client from './client'
import type { Diagnosis, PaginatedResponse } from '@/types'

export const getDiagnoses = (patientId: number, params?: Record<string, any>) =>
  client.get<PaginatedResponse<Diagnosis>>(`/patients/${patientId}/diagnoses`, { params })

export const getDiagnosis = (id: number) =>
  client.get<Diagnosis>(`/diagnoses/${id}`)

export const createDiagnosis = (patientId: number, data: Partial<Diagnosis>) =>
  client.post<{ data: Diagnosis }>(`/patients/${patientId}/diagnoses`, data)

export const updateDiagnosis = (id: number, data: Partial<Diagnosis>) =>
  client.put<{ data: Diagnosis }>(`/diagnoses/${id}`, data)
