import client from './client'
import type { Chat, Message, PaginatedResponse } from '@/types'

export const getChats = (params?: Record<string, any>) =>
  client.get<PaginatedResponse<Chat>>('/chats', { params })

export const getChat = (id: number) =>
  client.get<Chat>(`/chats/${id}`)

export const createChat = (data: { patient_id: number; doctor_id: number }) =>
  client.post<{ data: Chat }>('/chats', data)

export const getMessages = (chatId: number, params?: Record<string, any>) =>
  client.get<PaginatedResponse<Message>>(`/chats/${chatId}/messages`, { params })

export const sendMessage = (chatId: number, data: { content?: string; file_url?: string }) =>
  client.post<{ data: Message }>(`/chats/${chatId}/messages`, data)

export const markMessageRead = (messageId: number) =>
  client.patch<{ data: Message }>(`/messages/${messageId}/read`)
