<template>
  <div class="notifications-panel">
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Notifications</h3>
        <button 
          v-if="notifications.length > 0"
          @click="markAllAsRead" 
          class="text-sm text-purple-600 hover:text-purple-800"
        >
          Mark all as read
        </button>
      </div>
      
      <div class="border-t border-gray-200">
        <div v-if="notifications.length === 0" class="px-4 py-5 text-center text-gray-500">
          No notifications yet
        </div>
        
        <div v-else class="divide-y divide-gray-200">
          <div 
            v-for="notification in notifications" 
            :key="notification.id"
            :class="[
              'px-4 py-4 hover:bg-gray-50 transition-colors duration-150',
              { 'bg-purple-50': !notification.read_at }
            ]"
          >
            <div class="flex items-start">
              <div class="flex-1">
                <p class="text-sm text-gray-900">{{ notification.data.message }}</p>
                <div class="mt-1 flex items-center text-xs text-gray-500">
                  <span>{{ formatDate(notification.created_at) }}</span>
                  <span v-if="notification.data.bonus_amount" class="ml-2">
                    Bonus: {{ formatCurrency(notification.data.bonus_amount) }}
                  </span>
                </div>
              </div>
              <button 
                v-if="!notification.read_at"
                @click="markAsRead(notification.id)"
                class="ml-4 text-sm text-purple-600 hover:text-purple-800"
              >
                Mark as read
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const notifications = ref([]);
const page = usePage();

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('de-CH', {
    style: 'currency',
    currency: 'CHF'
  }).format(amount);
};

const markAsRead = async (notificationId) => {
  try {
    await axios.post(`/notifications/${notificationId}/mark-as-read`);
    notifications.value = notifications.value.map(notification => 
      notification.id === notificationId 
        ? { ...notification, read_at: new Date().toISOString() }
        : notification
    );
  } catch (error) {
    console.error('Error marking notification as read:', error);
  }
};

const markAllAsRead = async () => {
  try {
    await axios.post('/notifications/mark-all-as-read');
    notifications.value = notifications.value.map(notification => ({
      ...notification,
      read_at: new Date().toISOString()
    }));
  } catch (error) {
    console.error('Error marking all notifications as read:', error);
  }
};

onMounted(() => {
  // Load notifications from the page props
  if (page.props.notifications) {
    notifications.value = page.props.notifications;
  }
});
</script> 