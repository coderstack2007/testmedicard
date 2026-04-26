import client from './client'

export const createUser = async (data: {
  name: string
  email: string
  password: string
  role: 'patient' | 'doctor' | 'moderator'
}) => {
  const response = await client.post('/users', data)
  console.log('📡 createUser response:', response.data)
  return response.data
}