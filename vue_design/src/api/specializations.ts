import client from './client'

export const getSpecializations = async () => {
  const response = await client.get('/specializations')
  return response.data // ← добавьте .data
}