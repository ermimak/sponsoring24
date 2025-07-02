<template>
  <AppLayout title="Dashboard">
    <template #header>
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">
          Dashboard
        </h1>
        <div class="flex space-x-3">
          <button class="px-4 py-2 bg-white shadow-sm rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filter
          </button>
          <button class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 shadow-sm rounded-lg text-white hover:from-purple-700 hover:to-blue-700 transition-colors duration-200 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            New Campaign
          </button>
        </div>
      </div>
    </template>

    <div class="mb-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Stats Cards -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <h3 class="text-gray-500 text-sm font-medium">Projects</h3>
            <div class="bg-purple-100 rounded-full p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-bold">{{ props.stats.projectsCount }}</span>
            <span v-if="props.stats.activeLicense" class="text-green-500 text-sm ml-2">License Active</span>
            <span v-else class="text-yellow-500 text-sm ml-2">No Active License</span>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <h3 class="text-gray-500 text-sm font-medium">Total Donations</h3>
            <div class="bg-blue-100 rounded-full p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-bold">{{ formatCurrency(props.stats.totalDonations) }}</span>
            <span v-if="props.recentProjects.length > 0" class="text-green-500 text-sm ml-2">{{ props.recentProjects.length }} active projects</span>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <h3 class="text-gray-500 text-sm font-medium">Referral Earnings</h3>
            <div class="bg-emerald-100 rounded-full p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-3xl font-bold">{{ formatCurrency(props.stats.referrals.totalEarned) }}</span>
            <span class="text-green-500 text-sm ml-2">{{ props.stats.referrals.creditedReferrals }} credited</span>
            <span v-if="props.stats.referrals.pendingReferrals > 0" class="text-yellow-500 text-sm ml-2">{{ props.stats.referrals.pendingReferrals }} pending</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Projects Panel -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-semibold text-lg text-gray-800">Recent Projects</h2>
            <Link :href="route('dashboard.projects')" class="text-sm text-blue-600 hover:text-blue-800">View All</Link>
          </div>
          <div class="p-6">
            <div v-if="props.recentProjects.length === 0" class="text-center py-8 text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <p>No projects yet</p>
              <Link :href="route('dashboard.projects.create')" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700">
                Create Your First Project
              </Link>
            </div>
            <div v-else class="space-y-4">
              <div v-for="project in props.recentProjects" :key="project.id" class="flex items-center p-4 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                <div v-if="project.image" class="w-12 h-12 rounded-lg bg-cover bg-center mr-4" :style="{ backgroundImage: `url(${project.image})` }"></div>
                <div v-else class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold mr-4">
                  {{ project.name.substring(0, 2).toUpperCase() }}
                </div>
                <div class="flex-1">
                  <h3 class="font-medium">{{ project.name }}</h3>
                  <p class="text-sm text-gray-500">Last updated {{ formatRelativeDate(project.updated_at) }}</p>
                </div>
                <div class="text-right">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(project.status)">
                    {{ project.status.charAt(0).toUpperCase() + project.status.slice(1) }}
                  </span>
                  <p class="text-sm text-gray-500 mt-1">{{ formatCurrency(project.total_raised) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 h-full">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-semibold text-lg text-gray-800">Notifications</h2>
            <div class="relative">
              <NotificationDropdown :notifications="props.notifications" />
            </div>
          </div>
          <div class="p-6">
            <div v-if="props.notifications.length === 0" class="text-center py-8 text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <p>No notifications yet</p>
            </div>
            <div v-else class="space-y-4">
              <div v-for="notification in props.notifications.slice(0, 3)" :key="notification.id" 
                   class="p-3 rounded-lg hover:bg-gray-50 transition-colors duration-150"
                   :class="{'bg-blue-50': !notification.read_at}">
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <p class="text-sm font-medium" :class="notification.read_at ? 'text-gray-700' : 'text-gray-900'">
                      {{ notification.data.title || getNotificationTitle(notification) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                      {{ notification.data.message || getNotificationMessage(notification) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                      {{ formatRelativeDate(notification.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
              <div v-if="props.notifications.length > 3" class="text-center pt-2">
                <Link :href="route('dashboard.notifications.index')" class="text-xs text-blue-600 hover:text-blue-800">
                  View all {{ props.notifications.length }} notifications
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from '@/ziggy-plugin';
import AppLayout from '@/Layouts/AppDashboardLayout.vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';

// Define props for data passed from controller
const props = defineProps({
  notifications: {
    type: Array,
    default: () => []
  },
  unreadNotificationsCount: {
    type: Number,
    default: 0
  },
  stats: {
    type: Object,
    default: () => ({
      activeLicense: null,
      projectsCount: 0,
      totalDonations: 0,
      referrals: {
        totalReferrals: 0,
        creditedReferrals: 0,
        pendingReferrals: 0,
        totalEarned: 0
      },
      newUsersCount: 0,
      newUsersGrowth: 0
    })
  },
  recentProjects: {
    type: Array,
    default: () => []
  }
});

// Format currency with appropriate symbol
const formatCurrency = (amount, currency = 'CHF') => {
  return new Intl.NumberFormat('de-CH', {
    style: 'currency',
    currency: currency,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
};

// Format date to relative time
const formatRelativeDate = (dateString) => {
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
  
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

// Get project status class
const getStatusClass = (status) => {
  switch (status) {
    case 'active':
      return 'bg-green-100 text-green-800';
    case 'pending':
      return 'bg-yellow-100 text-yellow-800';
    case 'completed':
      return 'bg-blue-100 text-blue-800';
    case 'rejected':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};
</script> 