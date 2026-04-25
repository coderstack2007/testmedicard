import axios from 'axios'

const client = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor — add token to headers
client.interceptors.request.use((config) => {
  const token = localStorage.getItem('medicard_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Response interceptor — handle 401, convert response to data
// client.ts
client.interceptors.response.use(
  (response) => {
    return response  // ← возвращаем как есть, не трогаем
  },
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('medicard_token')
      localStorage.removeItem('medicard_user')
      window.location.href = '/login'
    }
    return Promise.reject(error)  // ← reject оригинальный error, не .data
  }
)

export default client
