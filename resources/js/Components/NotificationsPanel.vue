<template>
  <div class="notifications-panel">
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
      <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-semibold text-lg text-gray-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          Notifications
        </h3>
        <button 
          v-if="notifications.length > 0"
          @click="markAllAsRead" 
          class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200"
        >
          Mark all as read
        </button>
      </div>
      
      <div>
        <div v-if="notifications.length === 0" class="px-6 py-8 text-center">
          <div class="flex flex-col items-center justify-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-gray-500">No notifications yet</p>
          </div>
        </div>
        
        <div v-else class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
          <div 
            v-for="notification in notifications" 
            :key="notification.id"
            :class="[
              'px-6 py-4 hover:bg-gray-50 transition-colors duration-150',
              { 'bg-gradient-to-r from-purple-50 to-blue-50 border-l-4 border-blue-500': !notification.read_at }
            ]"
          >
            <div class="flex items-start">
              <div class="flex-shrink-0 mr-3">
                <div :class="[!notification.read_at ? 'bg-gradient-to-r from-purple-500 to-blue-500' : 'bg-gray-200', 'rounded-full p-2']">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">{{ notification.data.message }}</p>
                <div class="mt-1 flex items-center text-xs text-gray-500">
                  <span class="font-medium">{{ formatDate(notification.created_at) }}</span>
                  <span v-if="notification.data.bonus_amount" class="ml-2 px-2 py-0.5 rounded-full bg-green-100 text-green-800 font-medium">
                    {{ formatCurrency(notification.data.bonus_amount) }}
                  </span>
                </div>
              </div>
              <button 
                v-if="!notification.read_at"
                @click="markAsRead(notification.id)"
                class="ml-4 text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200 bg-white/80 px-3 py-1 rounded-full shadow-sm border border-blue-100"
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