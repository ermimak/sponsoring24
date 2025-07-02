<template>
  <div class="relative">
    <button 
      @click="isOpen = !isOpen" 
      class="flex items-center relative p-2 rounded-full hover:bg-gray-100 transition-colors duration-200"
      :class="{'bg-gray-100': unreadCount > 0}"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <div v-if="isOpen" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50">
      <div class="py-2 border-b border-gray-100">
        <div class="px-4 flex justify-between items-center">
          <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
          <button 
            v-if="unreadCount > 0" 
            @click="markAllAsRead" 
            class="text-xs text-blue-600 hover:text-blue-800"
          >
            Mark all as read
          </button>
        </div>
      </div>

      <div class="max-h-72 overflow-y-auto">
        <div v-if="!hasNotifications" class="py-4 px-4 text-center text-sm text-gray-500">
          No notifications
        </div>
        <div 
          v-for="notification in notificationsArray" 
          :key="notification.id" 
          class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors duration-200"
          :class="{'bg-blue-50': !notification.read_at}"
        >
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <p class="text-sm text-gray-800 font-medium">
                {{ getNotificationTitle(notification) }}
              </p>
              <p class="text-xs text-gray-500 mt-1">
                {{ getNotificationMessage(notification) }}
              </p>
              <p class="text-xs text-gray-400 mt-1">
                {{ formatDate(notification.created_at) }}
              </p>
            </div>
            <button 
              v-if="!notification.read_at" 
              @click.stop="markAsRead(notification)" 
              class="ml-2 text-xs text-blue-600 hover:text-blue-800"
            >
              Mark as read
            </button>
          </div>
        </div>
      </div>
      
      <div class="py-2 px-4 border-t border-gray-100 text-center">
        <Link :href="route('dashboard.notifications.index')" class="text-xs text-blue-600 hover:text-blue-800">
          View all notifications
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { route } from '@/ziggy-plugin';

const props = defineProps({
  notifications: {
    type: Array,
    default: () => []
  }
});

const isOpen = ref(false);

// Computed property to handle different notification data structures
const notificationsArray = computed(() => {
  // Check if notifications is an array
  if (Array.isArray(props.notifications)) {
    return props.notifications;
  }
  // Check if notifications has a data property that is an array (paginated response)
  else if (props.notifications && props.notifications.data && Array.isArray(props.notifications.data)) {
    return props.notifications.data;
  }
  // Default to empty array
  return [];
});

// Check if we have any notifications to display
const hasNotifications = computed(() => {
  return notificationsArray.value.length > 0;
});

// Count unread notifications
const unreadCount = computed(() => notificationsArray.value.filter(n => !n.read_at).length);

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (isOpen.value && !event.target.closest('.relative')) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  
  // Debug notifications
  console.log('Notifications prop:', props.notifications);
  console.log('Notifications type:', typeof props.notifications);
  console.log('Is Array?', Array.isArray(props.notifications));
  console.log('Length:', props.notifications ? props.notifications.length : 'N/A');
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Format date to relative time (e.g. "2 hours ago")
const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInSeconds = Math.floor((now - date) / 1000);
  
  if (diffInSeconds < 60) {
    return 'just now';
  }
  
  const diffInMinutes = Math.floor(diffInSeconds / 60);
  if (diffInMinutes < 60) {
    return `${diffInMinutes} ${diffInMinutes === 1 ? 'minute' : 'minutes'} ago`;
  }
  
  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24) {
    return `${diffInHours} ${diffInHours === 1 ? 'hour' : 'hours'} ago`;
  }
  
  const diffInDays = Math.floor(diffInHours / 24);
  if (diffInDays < 30) {
    return `${diffInDays} ${diffInDays === 1 ? 'day' : 'days'} ago`;
  }
  
  const diffInMonths = Math.floor(diffInDays / 30);
  if (diffInMonths < 12) {
    return `${diffInMonths} ${diffInMonths === 1 ? 'month' : 'months'} ago`;
  }
  
  const diffInYears = Math.floor(diffInMonths / 12);
  return `${diffInYears} ${diffInYears === 1 ? 'year' : 'years'} ago`;
};

// Get notification title based on type
const getNotificationTitle = (notification) => {
  try {
    if (!notification) {
      return 'Notification';
    }
    
    // Handle case where notification.data might be a string (JSON)
    let data;
    if (typeof notification.data === 'string') {
      try {
        data = JSON.parse(notification.data);
      } catch (e) {
        data = {};
      }
    } else {
      data = notification.data || {};
    }
    
    // If no type, return a default or message
    if (!data || !data.type) {
      return data && data.message ? data.message : 'Notification';
    }
    
    switch (data.type) {
      case 'account_approval':
        return 'Account Approved';
      case 'account_pending':
        return 'Account Pending';
      case 'account_rejection':
        return 'Account Rejected';
      case 'payment_received':
        return 'Payment Received';
      case 'project_update':
        return 'Project Update';
      case 'new_donation':
        return 'New Donation';
      default:
        return data.message || 'Notification';
    }
  } catch (error) {
    console.error('Error in getNotificationTitle:', error);
    return 'Notification';
  }
};

// Get notification message
const getNotificationMessage = (notification) => {
  try {
    if (!notification) {
      return '';
    }
    
    // Handle case where notification.data might be a string (JSON)
    let data;
    if (typeof notification.data === 'string') {
      try {
        data = JSON.parse(notification.data);
      } catch (e) {
        data = {};
      }
    } else {
      data = notification.data || {};
    }
    
    return data.message || '';  
  } catch (error) {
    console.error('Error in getNotificationMessage:', error);
    return '';
  }
};

// Mark notification as read
const markAsRead = (notification) => {
  router.post(route('dashboard.notifications.mark-as-read', { notification: notification.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      notification.read_at = new Date().toISOString();
    }
  });
};

// Mark all notifications as read
const markAllAsRead = () => {
  router.post(route('dashboard.notifications.mark-all-as-read'), {}, {
    preserveScroll: true,
    onSuccess: () => {
      // Update the notification read status based on the structure
      notificationsArray.value.forEach(notification => {
        if (!notification.read_at) {
          notification.read_at = new Date().toISOString();
        }
      });
    }
  });
};
</script>
