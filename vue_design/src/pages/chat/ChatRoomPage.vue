<template>
  <AppLayout>
    <div class="page-container">
      <router-link to="/chats" class="back-link">
        <ChevronLeft :size="18" />
        Back to Messages
      </router-link>

      <LoadingSpinner v-if="isLoading" message="Loading chat..." />

      <div v-else-if="chat" class="chat-room">
        <div class="chat-header">
          <h1>{{ chat.patient_profile?.user?.name || 'Patient' }}</h1>
        </div>

        <div class="messages-container">
          <div
            v-for="message in chat.messages"
            :key="message.id"
            class="message"
            :class="{ 'message-mine': isMyMessage(message.user_id) }"
          >
            <div class="message-content">{{ message.content }}</div>
            <span class="message-time">{{ formatTime(message.created_at) }}</span>
          </div>
        </div>

        <form @submit.prevent="sendMessage" class="message-form">
          <BaseInput
            v-model="newMessage"
            placeholder="Type your message..."
            @keyup.enter="sendMessage"
          />
          <BaseButton type="submit" variant="primary" :disabled="!newMessage">
            <Send :size="18" />
          </BaseButton>
        </form>
      </div>

      <div v-else class="not-found">
        <AlertCircle :size="48" />
        <p>Chat not found</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { ChevronLeft, Send, AlertCircle } from 'lucide-vue-next'
import type { Chat } from '@/types'

const route = useRoute()
const authStore = useAuthStore()

const chat = ref<Chat | null>(null)
const isLoading = ref(true)
const newMessage = ref('')

const isMyMessage = (userId: number) => {
  return userId === authStore.user?.id
}

const formatTime = (date: string) => {
  return new Date(date).toLocaleTimeString()
}

const sendMessage = async () => {
  // TODO: Send message via API
  // await messagesApi.store(chat.value!.id, { content: newMessage.value })
  // Fetch updated chat
  newMessage.value = ''
}

onMounted(async () => {
  // TODO: Fetch chat from API
  // const id = route.params.id
  // const response = await chatsApi.show(id)
  // chat.value = response.data
  isLoading.value = false
})
</script>

<style scoped>
.page-container {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
  height: 100%;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: var(--space-xs);
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 500;
  transition: all var(--transition-fast);
  width: fit-content;
}

.back-link:hover {
  gap: var(--space-sm);
}

.chat-room {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
  flex: 1;
}

.chat-header h1 {
  margin: 0;
  font-size: var(--font-xl);
}

.messages-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
  padding: var(--space-md);
  background: var(--color-neutral-50);
  border-radius: var(--radius-card);
  overflow-y: auto;
  max-height: 500px;
}

.message {
  display: flex;
  flex-direction: column;
  gap: var(--space-xs);
  align-items: flex-start;
}

.message-mine {
  align-items: flex-end;
}

.message-content {
  background: var(--color-info);
  color: white;
  padding: var(--space-md) var(--space-lg);
  border-radius: var(--radius-button);
  max-width: 70%;
  word-wrap: break-word;
}

.message-mine .message-content {
  background: var(--color-primary);
}

.message-time {
  font-size: var(--font-xs);
  color: var(--color-neutral-500);
}

.message-form {
  display: flex;
  gap: var(--space-md);
}

.message-form input {
  flex: 1;
}

.not-found {
  text-align: center;
  padding: var(--space-4xl) var(--space-2xl);
  color: var(--color-neutral-500);
}

.not-found svg {
  margin-bottom: var(--space-md);
  opacity: 0.5;
}
</style>
