<template>
  <AuthLayout :title="$t('auth.forgot_password')">
    <div class="bg-white p-8 md:p-10 rounded-xl shadow-2xl border border-gray-200 w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ $t('auth.forgot_password') }}
        </h1>
        <p class="text-gray-500 mt-2 text-sm">{{ $t('auth.forgot_password_description') }}</p>
      </div>

      <!-- Status Message -->
      <div v-if="status" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-md">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-green-700">{{ status }}</p>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
            </svg>
            {{ $t('auth.email') }}
          </label>
          <div class="relative">
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              autofocus
              placeholder="your.email@example.com"
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
            />
          </div>
          <div v-if="form.errors.email" class="mt-1.5 text-xs text-red-600 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ form.errors.email }}
          </div>
        </div>

        <div class="mt-8">
          <button
            type="submit"
            class="w-full flex justify-center items-center py-3 px-4 rounded-lg text-base font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
            :disabled="form.processing"
          >
            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ $t('auth.send_reset_link') }}
          </button>
        </div>

        <div class="mt-6 text-center">
          <Link
            :href="route('login')"
            class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center justify-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ $t('auth.back_to_login') }}
          </Link>
        </div>
      </form>
    </div>
  </AuthLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from '@/ziggy-plugin';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
  status: String,
});

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'), {
    onSuccess: () => {
      form.reset('email');
    }
  });
};
</script>
