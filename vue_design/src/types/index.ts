export type UserRole = 'patient' | 'doctor' | 'moderator' | 'absolute_admin'
export type DiagnosisStatus = 'active' | 'completed' | 'archived'
export type MessageStatus = 'sent' | 'delivered' | 'read'

export interface User {
  id: number
  name: string
  email: string
  role: UserRole
  is_active: boolean
  created_at?: string
  updated_at?: string
  doctor_profile?: DoctorProfile
  patient_profile?: PatientProfile
}

export interface Branch {
  id: number
  name: string
  address?: string
  code?: string
  moderator_id?: number
  is_active: boolean
  created_at?: string
  updated_at?: string
  departments?: Department[]
  moderator?: User
}

export interface Department {
  id: number
  branch_id: number
  name: string
  description?: string
  created_at?: string
  updated_at?: string
  branch?: Branch
}

export interface DoctorProfile {
  id: number
  user_id: number
  branch_id: number
  department_id: number
  specialization?: string
  phone?: string
  created_at?: string
  updated_at?: string
  user?: User
  branch?: Branch
  department?: Department
}

export interface PatientProfile {
  id: number
  user_id: number
  branch_id: number
  date_of_birth?: string
  blood_type?: string
  created_at?: string
  updated_at?: string
  user?: User
  branch?: Branch
}

export interface Diagnosis {
  id: number
  patient_id: number
  doctor_id: number
  icd_code: string
  description: string
  status: DiagnosisStatus
  diagnosis_date: string
  created_at?: string
  updated_at?: string
  patient?: User
  doctor?: User
}

export interface Chat {
  id: number
  patient_id: number
  doctor_id: number
  created_at?: string
  updated_at?: string
  patient?: User
  doctor?: User
  last_message?: Message
}

export interface Message {
  id: number
  chat_id: number
  sender_id: number
  content?: string
  file_url?: string
  status: MessageStatus
  created_at?: string
  updated_at?: string
  sender?: User
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  total: number
  per_page: number
  from?: number
  to?: number
  next_page_url?: string
  prev_page_url?: string
}

export interface ApiResponse<T> {
  data?: T
  message?: string
  error?: string
}
