<template>
  <DashboardLayout title="License Purchase Successful">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        License Purchase Successful
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div class="text-center">
              <CheckCircleIcon class="h-16 w-16 text-green-500 mx-auto" />
              <h2 class="mt-4 text-2xl font-bold text-gray-900">Thank you for your purchase!</h2>
              <p class="mt-2 text-lg text-gray-600">
                Your license has been successfully activated.
              </p>
            </div>

            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900">License Details</h3>
              
              <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <div class="text-sm font-medium text-gray-500">License Type</div>
                  <div class="mt-1 text-sm text-gray-900">{{ license?.type || 'Annual' }}</div>
                </div>
                
                <div>
                  <div class="text-sm font-medium text-gray-500">Status</div>
                  <div class="mt-1 text-sm text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      {{ license?.status || 'Active' }}
                    </span>
                  </div>
                </div>
                
                <div>
                  <div class="text-sm font-medium text-gray-500">Valid Until</div>
                  <div class="mt-1 text-sm text-gray-900">
                    {{ license?.expires_at ? formatDate(license.expires_at) : 'One year from today' }}
                  </div>
                </div>
                
                <div>
                  <div class="text-sm font-medium text-gray-500">Purchase Date</div>
                  <div class="mt-1 text-sm text-gray-900">
                    {{ license?.created_at ? formatDate(license.created_at) : 'Today' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-8 flex justify-center">
              <Link :href="route('dashboard')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Go to Dashboard
              </Link>
              <Link :href="route('dashboard.license')" class="ml-4 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                View License Details
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { CheckCircleIcon } from '@heroicons/vue/24/outline';
import { route } from '@/ziggy-plugin';
defineProps({
  license: Object,
  user: Object,
});

function formatDate(dateString) {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(date);
}
</script>
