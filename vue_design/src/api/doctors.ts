import client from './client'

export const getDoctors = async (branchId: number) => {
  const response = await client.get(`/branches/${branchId}/doctors`)
  return response
}

export const createDoctor = async (data: {
  user_id: number
  branch_id: number | string
  department_id: number | string
  specialization?: string
  phone?: string
}) => {
  const response = await client.post('/doctors', data)
  return response.data
}

export const getDoctor = async (id: number) => {
  const response = await client.get(`/doctors/${id}`)
  return response.data
}

export const updateDoctor = async (id: number, data: any) => {
  const response = await client.put(`/doctors/${id}`, data)
  return response.data
}

export const deleteDoctor = async (id: number) => {
  await client.delete(`/doctors/${id}`)
}