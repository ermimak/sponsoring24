<template>
  <DashboardLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $t('License Details') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-6">
              <Link :href="route('license.index')" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ $t('Back to License Dashboard') }}
              </Link>
            </div>

            <div v-if="license" class="mb-8">
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">{{ $t('License Information') }}</h3>
                <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium" v-if="license.status === 'active'">
                  {{ $t('Active') }}
                </div>
                <div class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium" v-else>
                  {{ $t('Inactive') }}
                </div>
              </div>
              
              <div class="bg-gray-50 p-6 rounded-lg mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('License Type') }}</p>
                    <p class="font-medium">{{ license.type === 'annual' ? $t('Annual License') : $t('Monthly License') }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('License Key') }}</p>
                    <p class="font-mono bg-gray-100 px-2 py-1 rounded text-sm overflow-auto">{{ license.license_key }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Issue Date') }}</p>
                    <p class="font-medium">{{ formatDate(license.issued_at) }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Expiry Date') }}</p>
                    <p class="font-medium">{{ formatDate(license.expires_at) }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Days Remaining') }}</p>
                    <p class="font-medium">{{ license.days_remaining }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Payment ID') }}</p>
                    <p class="font-medium text-sm overflow-auto">{{ license.payment_id }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 p-6 rounded-lg">
                <h4 class="font-medium text-gray-800 mb-4">{{ $t('Payment Details') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Amount Paid') }}</p>
                    <p class="font-medium">{{ license.currency }} {{ license.amount.toFixed(2) }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Discount Applied') }}</p>
                    <p class="font-medium">
                      <template v-if="license.discount_applied">
                        {{ $t('Yes') }} ({{ license.currency }} {{ license.discount_amount.toFixed(2) }})
                      </template>
                      <template v-else>{{ $t('No') }}</template>
                    </p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Purchase Date') }}</p>
                    <p class="font-medium">{{ formatDate(license.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-yellow-700">
                    {{ $t('No license information found. Please purchase a license to view details.') }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  license: Object
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
</script>
