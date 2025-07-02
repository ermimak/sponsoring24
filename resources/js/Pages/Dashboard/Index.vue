<template>
    <DashboardLayout>
        <!-- Debug Info -->
        <div v-if="$page.props?.auth?.user?.email === 'admin@example.com'" class="mb-4 p-4 bg-gray-100 rounded-lg text-xs overflow-auto max-h-40">
            <p class="font-bold">Debug Info:</p>
            <p>Projects Count: {{ props.stats?.projectsCount || 0 }}</p>
            <p>Total Donations: {{ props.stats?.totalDonations || 0 }}</p>
            <p>Recent Projects Count: {{ props.recentProjects?.length || 0 }}</p>
            <p>Recent Projects: {{ JSON.stringify(props.recentProjects || []) }}</p>
        </div>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <!-- Stats -->
                <div class="mt-8">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Total Donations -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400"
fill="none"
viewBox="0 0 24 24"
stroke="currentColor">
                                            <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total Donations</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.totalDonations) }}</div>
                                                <div v-if="stats.donationsGrowth !== 0" class="ml-2 flex items-baseline text-sm font-semibold" :class="stats.donationsGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                                                    <svg v-if="stats.donationsGrowth >= 0" class="self-center flex-shrink-0 h-5 w-5" :class="stats.donationsGrowth >= 0 ? 'text-green-500' : 'text-red-500'" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <svg v-else class="self-center flex-shrink-0 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="sr-only">{{ stats.donationsGrowth >= 0 ? 'Increased' : 'Decreased' }} by</span>
                                                    {{ Math.abs(stats.donationsGrowth) }}%
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Projects -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400"
fill="none"
viewBox="0 0 24 24"
stroke="currentColor">
                                            <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Active Projects</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ stats.projectsCount }}</div>
                                                <div v-if="stats.projectsGrowth !== 0" class="ml-2 flex items-baseline text-sm font-semibold" :class="stats.projectsGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                                                    <svg v-if="stats.projectsGrowth >= 0" class="self-center flex-shrink-0 h-5 w-5" :class="stats.projectsGrowth >= 0 ? 'text-green-500' : 'text-red-500'" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <svg v-else class="self-center flex-shrink-0 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="sr-only">{{ stats.projectsGrowth >= 0 ? 'Increased' : 'Decreased' }} by</span>
                                                    {{ Math.abs(stats.projectsGrowth) }}
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Donors -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400"
fill="none"
viewBox="0 0 24 24"
stroke="currentColor">
                                            <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total Donors</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ stats.referrals.totalReferrals }}</div>
                                                <div v-if="stats.referralsGrowth !== 0" class="ml-2 flex items-baseline text-sm font-semibold" :class="stats.referralsGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                                                    <svg v-if="stats.referralsGrowth >= 0" class="self-center flex-shrink-0 h-5 w-5" :class="stats.referralsGrowth >= 0 ? 'text-green-500' : 'text-red-500'" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <svg v-else class="self-center flex-shrink-0 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="sr-only">{{ stats.referralsGrowth >= 0 ? 'Increased' : 'Decreased' }} by</span>
                                                    {{ Math.abs(stats.referralsGrowth) }}%
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Success Rate -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400"
fill="none"
viewBox="0 0 24 24"
stroke="currentColor">
                                            <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Referral Earnings</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.referrals.totalEarned) }}</div>
                                                <div v-if="stats.referrals.pendingReferrals > 0" class="ml-2 flex items-baseline text-sm font-semibold text-yellow-600">
                                                    <svg class="self-center flex-shrink-0 h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="sr-only">Pending</span>
                                                    {{ stats.referrals.pendingReferrals }} pending
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Projects -->
                <div class="mt-8">
                    <h2 class="text-lg leading-6 font-medium text-gray-900">Recent Projects</h2>
                    <div class="mt-2 bg-white shadow overflow-hidden sm:rounded-md">
                        <ul v-if="recentActivity.length > 0" role="list" class="divide-y divide-gray-200">
                            <li v-for="project in recentActivity" :key="project.id">
                                <Link :href="route('dashboard.projects.show', project.id)" class="block hover:bg-gray-50">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-blue-600 truncate">
                                                {{ project.title }}
                                            </p>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="project.statusClass">
                                                    {{ project.status }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-2 sm:flex sm:justify-between">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    {{ project.description }}
                                                </p>
                                            </div>
                                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
fill="none"
viewBox="0 0 24 24"
stroke="currentColor">
                                                    <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p>
                                                    {{ project.date }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700">
                                            <span class="font-medium">{{ project.amount }}</span> raised
                                        </div>
                                    </div>
                                </Link>
                            </li>
                        </ul>
                        <div v-else class="py-8 text-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <p>No projects yet</p>
                            <Link :href="route('dashboard.projects.create')" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Create your first project
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="$page.props?.auth?.user?.permissions?.includes('manage_users')" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
            <div>
                <span class="font-semibold text-blue-800">Admin Quick Actions:</span>
                <a href="/dashboard/admin/roles" class="ml-4 text-blue-700 hover:underline">Manage Roles</a>
                <a href="/dashboard/admin/permissions" class="ml-4 text-blue-700 hover:underline">Manage Permissions</a>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';
import { route } from '@/ziggy-plugin';
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

// Map recent projects to activity items
const recentActivity = computed(() => {
  console.log('Recent projects from props:', props.recentProjects);
  
  if (!props.recentProjects || props.recentProjects.length === 0) {
    console.log('No recent projects found');
    return [];
  }
  
  return props.recentProjects.map(project => {
    console.log('Processing project:', project);
    return {
      id: project.id,
      title: project.name,
      description: `Last updated ${formatRelativeDate(project.updated_at)}`,
      status: project.status ? project.status.charAt(0).toUpperCase() + project.status.slice(1) : 'Unknown',
      statusClass: getStatusClass(project.status || 'unknown'),
      date: formatRelativeDate(project.updated_at),
      amount: formatCurrency(project.total_raised || 0)
    };
  });
});
</script> 