<template>
  <AdminLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $t('License Details') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
          <Link :href="route('admin.licenses.index')" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ $t('Back to Licenses') }}
          </Link>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium text-gray-900">{{ $t('License Information') }}</h3>
              <div class="flex items-center">
                <span :class="{
                  'px-3 py-1 rounded-full text-sm font-medium mr-3': true,
                  'bg-green-100 text-green-800': license.is_active,
                  'bg-red-100 text-red-800': license.is_expired || license.status === 'revoked',
                  'bg-yellow-100 text-yellow-800': !license.is_active && !license.is_expired && license.status !== 'revoked'
                }">
                  {{ license.status.charAt(0).toUpperCase() + license.status.slice(1) }}
                </span>
                <div class="flex space-x-2">
                  <button 
                    v-if="license.status !== 'active'" 
                    @click="updateStatus('active')"
                    class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm"
                  >
                    {{ $t('Activate') }}
                  </button>
                  <button 
                    v-if="license.status !== 'revoked'" 
                    @click="updateStatus('revoked')"
                    class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm"
                  >
                    {{ $t('Revoke') }}
                  </button>
                </div>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <p class="text-sm text-gray-500">{{ $t('License Key') }}</p>
                <p class="font-mono bg-gray-100 px-2 py-1 rounded text-sm overflow-auto">{{ license.license_key }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">{{ $t('Payment ID') }}</p>
                <p class="font-mono bg-gray-100 px-2 py-1 rounded text-sm overflow-auto">{{ license.payment_id || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">{{ $t('License Type') }}</p>
                <p class="font-medium">{{ license.type === 'annual' ? $t('Annual License') : $t('Monthly License') }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">{{ $t('Amount Paid') }}</p>
                <p class="font-medium">{{ license.currency }} {{ license.amount.toFixed(2) }}</p>
                <p v-if="license.discount_applied" class="text-sm text-green-600">
                  {{ $t('Discount Applied') }}: {{ license.currency }} {{ license.discount_amount.toFixed(2) }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500">{{ $t('Issue Date') }}</p>
                <p class="font-medium">{{ formatDate(license.issued_at) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">{{ $t('Expiry Date') }}</p>
                <p class="font-medium">{{ formatDate(license.expires_at) }}</p>
                <p v-if="license.days_remaining !== null" class="text-sm" :class="{'text-green-600': license.days_remaining > 30, 'text-yellow-600': license.days_remaining <= 30 && license.days_remaining > 0, 'text-red-600': license.days_remaining <= 0}">
                  {{ license.days_remaining > 0 ? `${license.days_remaining} ${$t('days remaining')}` : $t('Expired') }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500">{{ $t('Created At') }}</p>
                <p class="font-medium">{{ formatDateTime(license.created_at) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">{{ $t('Last Updated') }}</p>
                <p class="font-medium">{{ formatDateTime(license.updated_at) }}</p>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-6 mt-6">
              <h4 class="font-medium text-gray-800 mb-4">{{ $t('User Information') }}</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <p class="text-sm text-gray-500">{{ $t('Name') }}</p>
                  <p class="font-medium">{{ license.user?.name || 'N/A' }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">{{ $t('Email') }}</p>
                  <p class="font-medium">{{ license.user?.email || 'N/A' }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">{{ $t('User ID') }}</p>
                  <p class="font-medium">{{ license.user_id }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">{{ $t('User Since') }}</p>
                  <p class="font-medium">{{ license.user?.created_at ? formatDate(license.user.created_at) : 'N/A' }}</p>
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('admin.users.show', license.user_id)" class="text-blue-600 hover:text-blue-800">
                  {{ $t('View User Profile') }}
                </Link>
              </div>
            </div>

            <div v-if="license.metadata" class="border-t border-gray-200 pt-6 mt-6">
              <h4 class="font-medium text-gray-800 mb-4">{{ $t('Additional Metadata') }}</h4>
              <pre class="bg-gray-100 p-4 rounded-md overflow-auto text-sm">{{ JSON.stringify(license.metadata, null, 2) }}</pre>
            </div>
          </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Activity Log') }}</h3>
            
            <div v-if="activities.length === 0" class="text-gray-500">
              {{ $t('No activities recorded for this license.') }}
            </div>
            
            <div v-else class="space-y-4">
              <div v-for="(activity, index) in activities" :key="index" class="border-l-2 border-gray-200 pl-4 py-2">
                <div class="flex items-start">
                  <div class="flex-shrink-0 h-4 w-4 rounded-full bg-blue-500 mt-1"></div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ activity.description }}</p>
                    <p class="text-xs text-gray-500">{{ formatDateTime(activity.created_at) }}</p>
                    <div v-if="activity.properties" class="mt-1 text-xs text-gray-600">
                      <pre class="bg-gray-50 p-2 rounded">{{ JSON.stringify(activity.properties, null, 2) }}</pre>
                    </div>
                  </div>
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
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  license: Object,
  activities: Array
});

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(date);
};

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false
  }).format(date);
};

const updateStatus = (status) => {
  const form = useForm({
    status: status,
  });
  
  form.post(route('admin.licenses.update-status', props.license.id), {
    preserveScroll: true,
  });
};
</script>
