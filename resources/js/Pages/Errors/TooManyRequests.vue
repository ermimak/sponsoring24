<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl p-8 md:p-10 border border-gray-200">
      <div class="text-center">
        <div class="flex justify-center mb-6">
          <div class="rounded-full bg-orange-100 p-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        
        <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-2">
          429
        </h1>
        
        <p class="text-xl font-semibold text-gray-800 mb-6">Too Many Requests</p>
        
        <p class="text-gray-600 mb-8">{{ message || 'You have made too many requests recently. Please wait a moment before trying again.' }}</p>
        
        <div class="flex flex-col space-y-4">
          <button
            @click="goBack"
            class="w-full flex justify-center items-center py-3 px-4 rounded-lg text-base font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 shadow-lg hover:shadow-xl transition-all duration-200"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ $t('common.go_back') }}
          </button>
          
          <Link
            :href="route('dashboard')"
            class="w-full flex justify-center items-center py-3 px-4 rounded-lg text-base font-semibold text-purple-600 bg-white border border-purple-300 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 shadow-sm hover:shadow transition-all duration-200"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-7-7v14" />
            </svg>
            {{ $t('common.go_to_dashboard') }}
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from '@/ziggy-plugin';

const props = defineProps({
  status: {
    type: Number,
    default: 429
  },
  message: {
    type: String,
    default: 'You have made too many requests recently. Please wait a moment before trying again.'
  }
});

const goBack = () => {
  window.history.length > 1 ? window.history.go(-1) : window.location.href = route('dashboard');
};
</script>
