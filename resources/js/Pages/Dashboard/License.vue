<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">License Management</h1>
        <p class="text-gray-600 mt-2">View and manage your Sponsoring24 license details</p>
      </div>

      <div v-if="license" class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-200">
        <!-- License Status -->
        <div class="mb-6">
          <div class="flex items-center mb-4">
            <div v-if="license.status === 'active'" class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Active
            </div>
            <div v-else-if="license.status === 'expired'" class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Expired
            </div>
            <div v-else-if="license.status === 'revoked'" class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
              </svg>
              Revoked
            </div>
            <div v-if="license.days_remaining > 0" class="ml-4 text-sm text-gray-600">
              {{ license.days_remaining }} days remaining
            </div>
          </div>
        </div>

        <!-- License Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <h3 class="text-lg font-semibold mb-4 text-gray-800">License Information</h3>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-500">License Key</label>
                <div class="flex items-center mt-1">
                  <div class="bg-gray-50 px-3 py-2 rounded-md border border-gray-200 text-sm font-mono text-gray-800 flex-grow">
                    {{ license.license_key }}
                  </div>
                  <button 
                    @click="copyLicenseKey" 
                    class="ml-2 p-2 text-purple-600 hover:text-purple-800 transition-colors duration-200"
                    title="Copy to clipboard"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                  </button>
                </div>
                <p v-if="keyCopied" class="text-green-600 text-xs mt-1 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Copied to clipboard!
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Type</label>
                <p class="text-gray-800 capitalize">{{ license.type }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Issued Date</label>
                <p class="text-gray-800">{{ formatDate(license.issued_at) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Expiration Date</label>
                <p class="text-gray-800">{{ formatDate(license.expires_at) }}</p>
              </div>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Payment Information</h3>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-500">Amount Paid</label>
                <p class="text-gray-800">{{ license.currency }} {{ license.amount }}</p>
              </div>
              <div v-if="license.discount_applied">
                <label class="block text-sm font-medium text-gray-500">Discount Applied</label>
                <p class="text-gray-800">{{ license.currency }} {{ license.discount_amount }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Payment ID</label>
                <p class="text-gray-800 text-sm">{{ license.payment_id }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Purchase Date</label>
                <p class="text-gray-800">{{ formatDate(license.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Renewal Information -->
        <div v-if="license.status === 'active'" class="border-t border-gray-200 pt-6">
          <h3 class="text-lg font-semibold mb-4 text-gray-800">Renewal Information</h3>
          <p class="text-gray-600 mb-4">
            Your license will automatically renew on {{ formatDate(license.expires_at) }}. You will be notified before the renewal date.
          </p>
          <div class="flex space-x-4">
            <button class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-800 focus:outline-none focus:border-purple-800 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
              Manage Renewal
            </button>
            <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150">
              Download Invoice
            </button>
          </div>
        </div>

        <!-- Expired License Actions -->
        <div v-else-if="license.status === 'expired'" class="border-t border-gray-200 pt-6">
          <h3 class="text-lg font-semibold mb-4 text-gray-800">Renew Your License</h3>
          <p class="text-gray-600 mb-4">
            Your license has expired. Renew now to continue using all features of Sponsoring24.
          </p>
          <button class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-800 focus:outline-none focus:border-purple-800 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
            Renew License
          </button>
        </div>
      </div>

      <!-- No License State -->
      <div v-else class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-200">
        <div class="text-center py-8">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Active License</h3>
          <p class="text-gray-500 mb-6">You don't have an active license. Purchase a license to unlock all features of Sponsoring24.</p>
          <a 
            :href="route('license.purchase')" 
            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-800 focus:outline-none focus:border-purple-800 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150"
          >
            Purchase License
          </a>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { route } from '@/ziggy-plugin';

const props = defineProps({
  license: Object
})

const keyCopied = ref(false)

const copyLicenseKey = () => {
  if (props.license) {
    navigator.clipboard.writeText(props.license.license_key)
      .then(() => {
        keyCopied.value = true
        setTimeout(() => {
          keyCopied.value = false
        }, 3000)
      })
      .catch(err => {
        console.error('Failed to copy license key: ', err)
      })
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(date)
}
</script>
