import client from './client'
import type { User, ApiResponse } from '@/types'

export const register = async (data: { name: string; email: string; password: string; password_confirmation: string; role: 'patient' | 'doctor' }) => {
  const response = await client.post<ApiResponse<{ user: User; token: string }>>('/auth/register', data)
  return response.data
}

export const login = async (data: { email: string; password: string }) => {
  const response = await client.post<{ user: User; token: string }>('/auth/login', data)
  return response.data  // ← { user, token }
}

export const logout = async () => {
  await client.post('/auth/logout')
}

export const me = async () => {
  const response = await client.get<User>('/auth/me')
  return response.data
}
