import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Chat, Message } from '@/types'
import * as chatsApi from '@/api/chats'

export const useChatStore = defineStore('chat', () => {
  const chats = ref<Chat[]>([])
  const currentChat = ref<Chat | null>(null)
  const messages = ref<Message[]>([])
  const isLoading = ref(false)

  async function fetchChats() {
    isLoading.value = true
    try {
      const response = await chatsApi.getChats()
      chats.value = response.data || []
    } finally {
      isLoading.value = false
    }
  }

  async function fetchChat(id: number) {
    isLoading.value = true
    try {
      currentChat.value = await chatsApi.getChat(id)
    } finally {
      isLoading.value = false
    }
  }

  async function fetchMessages(chatId: number) {
    isLoading.value = true
    try {
      const response = await chatsApi.getMessages(chatId)
      messages.value = response.data || []
    } finally {
      isLoading.value = false
    }
  }

  async function createChat(patientId: number, doctorId: number) {
    isLoading.value = true
    try {
      const chat = await chatsApi.createChat({ patient_id: patientId, doctor_id: doctorId })
      chats.value.push(chat.data!)
      return chat.data
    } finally {
      isLoading.value = false
    }
  }

  async function sendMessage(chatId: number, content?: string, fileUrl?: string) {
    try {
      const message = await chatsApi.sendMessage(chatId, { content, file_url: fileUrl })
      messages.value.push(message.data!)
      return message.data
    } catch (error) {
      throw error
    }
  }

  async function markMessageRead(messageId: number) {
    try {
      const message = await chatsApi.markMessageRead(messageId)
      const index = messages.value.findIndex(m => m.id === messageId)
      if (index !== -1) {
        messages.value[index] = message.data!
      }
      return message.data
    } catch (error) {
      throw error
    }
  }

  return {
    chats,
    currentChat,
    messages,
    isLoading,
    fetchChats,
    fetchChat,
    fetchMessages,
    createChat,
    sendMessage,
    markMessageRead,
  }
})
