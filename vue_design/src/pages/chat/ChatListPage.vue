<template>
  <AppLayout>
    <div class="page-container">
      <div class="page-header">
        <h1>Messages</h1>
        <BaseButton variant="primary" @click="newChat">
          <Plus :size="18" />
          New Message
        </BaseButton>
      </div>

      <LoadingSpinner v-if="isLoading" message="Loading messages..." />

      <div v-else class="chats-list">
        <div
          v-for="chat in chats"
          :key="chat.id"
          class="chat-item"
          @click="selectChat(chat.id)"
        >
          <BaseCard class="cursor-pointer">
            <div class="chat-header">
              <h3>{{ chat.patient_profile?.user?.name || 'Unknown Patient' }}</h3>
              <span class="time">{{ formatDate(chat.created_at) }}</span>
            </div>
            <p class="chat-preview">{{ chat.messages?.[0]?.content || 'No messages yet' }}</p>
          </BaseCard>
        </div>
      </div>

      <div v-if="chats.length === 0 && !isLoading" class="empty-state">
        <MessageSquare :size="48" />
        <p>No conversations yet</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

import BaseCard from '@/components/ui/BaseCard.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { Plus, MessageSquare } from 'lucide-vue-next'
import type { Chat } from '@/types'

const router = useRouter()

const chats = ref<Chat[]>([])
const isLoading = ref(false)

const selectChat = (id: number) => {
  router.push(`/chats/${id}`)
}

const newChat = () => {
  // TODO: Open new chat modal
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

onMounted(async () => {
  // TODO: Fetch chats from API
  // const response = await chatsApi.list()
  // chats.value = response.data
  isLoading.value = false
})
</script>

<style scoped>
.page-container {
  display: flex;
  flex-direction: column;
  gap: var(--space-xl);
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-header h1 {
  font-size: var(--font-2xl);
  font-weight: 600;
  margin: 0;
}

.chats-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
}

.chat-item {
  cursor: pointer;
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-sm);
}

.chat-header h3 {
  margin: 0;
  font-size: var(--font-base);
  font-weight: 600;
}

.time {
  font-size: var(--font-xs);
  color: var(--color-neutral-500);
}

.chat-preview {
  font-size: var(--font-sm);
  color: var(--color-neutral-600);
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.empty-state {
  text-align: center;
  padding: var(--space-4xl) var(--space-2xl);
  color: var(--color-neutral-500);
}

.empty-state svg {
  margin-bottom: var(--space-md);
  opacity: 0.5;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
