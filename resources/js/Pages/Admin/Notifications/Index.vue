<template>
  <AdminLayout title="Notifications">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Notifications
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium text-gray-900">All Notifications</h3>
              <button 
                v-if="unreadCount > 0"
                @click="markAllAsRead" 
                class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white text-sm font-medium rounded-md hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Mark all as read
              </button>
            </div>

            <div v-if="!notifications.data || notifications.data.length === 0" class="text-center py-8 text-gray-500">
              No notifications to display
            </div>

            <div v-else class="space-y-4">
              <div 
                v-for="notification in notifications.data" 
                :key="notification.id" 
                class="p-4 border rounded-md transition-colors duration-200"
                :class="{'bg-blue-50 border-blue-200': !notification.read_at, 'border-gray-200': notification.read_at}"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="font-medium text-gray-900">
                      {{ getNotificationTitle(notification) }}
                    </h4>
                    <p class="text-sm text-gray-600 mt-1">
                      {{ getNotificationMessage(notification) }}
                    </p>
                    <div class="flex items-center mt-2">
                      <span class="text-xs text-gray-500">
                        {{ formatDate(notification.created_at) }}
                      </span>
                      <span v-if="notification.data.user_id" class="ml-3 px-2 py-1 bg-gray-100 text-xs rounded-full text-gray-600">
                        User ID: {{ notification.data.user_id }}
                      </span>
                    </div>
                  </div>
                  <button 
                    v-if="!notification.read_at" 
                    @click="markAsRead(notification)" 
                    class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Mark as read
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from '@/ziggy-plugin';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { formatDate } from '@/utils';
import { onMounted } from 'vue';

const props = defineProps({
  notifications: Object,
});

onMounted(() => {
  // Debug notifications
  console.log('Admin notifications:', props.notifications);
  if (props.notifications && props.notifications.data && props.notifications.data.length > 0) {
    console.log('First notification:', props.notifications.data[0]);
    console.log('First notification data:', props.notifications.data[0].data);
  }
});

const unreadCount = computed(() => props.notifications.data.filter(n => !n.read_at).length);

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
  if (!notification || !notification.data) {
    return 'Notification';
  }
  
  const data = notification.data;
  
  if (!data.type) {
    return data.message || 'Notification';
  }
  
  switch (data.type) {
    case 'user_registered':
      return 'New User Registration';
    case 'user_updated':
      return 'User Profile Updated';
    case 'project_created':
      return 'New Project Created';
    case 'project_updated':
      return 'Project Updated';
    case 'payment_received':
      return 'Payment Received';
    case 'new_donation':
      return 'New Donation';
    default:
      return data.message || 'Notification';
  }
};

// Get notification message
const getNotificationMessage = (notification) => {
  if (!notification || !notification.data) {
    return '';
  }
  return notification.data.message || '';
};

// Mark notification as read
const markAsRead = (notification) => {
  router.post(route('admin.notifications.mark-as-read', { notification: notification.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      notification.read_at = new Date().toISOString();
    }
  });
};

// Mark all notifications as read
const markAllAsRead = () => {
  router.post(route('admin.notifications.mark-all-as-read'), {}, {
    preserveScroll: true,
    onSuccess: () => {
      props.notifications.forEach(notification => {
        if (!notification.read_at) {
          notification.read_at = new Date().toISOString();
        }
      });
    }
  });
};
</script>
