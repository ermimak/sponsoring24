<template>
    <AuthLayout>
        <div class="bg-white p-8 md:p-10 rounded-xl shadow-2xl border border-gray-200 w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1m0-10V5a2 2 0 00-2-2h-3a2 2 0 00-2 2v1" />
                    </svg>
                    Member Login
                </h1>
                <p class="text-gray-500 mt-2 text-sm">Welcome back! Please enter your credentials.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                        E-Mail Address
                    </label>
                    <div class="relative">
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
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

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Password
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            placeholder="Enter your password"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                        />
                    </div>
                    <div v-if="form.errors.password" class="mt-1.5 text-xs text-red-600 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="flex items-center justify-between mt-5">
                    <div class="flex items-center">
                        <input
                            id="remember_me"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded shadow-sm"
                        />
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            Stay logged in
                        </label>
                    </div>
                    <div class="text-sm">
                        <a :href="route('password.request')" class="font-medium text-purple-600 hover:text-purple-500 hover:underline transition-colors duration-200">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a :href="route('password.request')" class="text-sm text-purple-600 hover:text-purple-500 hover:underline transition-colors duration-200">
                        Forgot your password?
                    </a>
                </div>

                <div class="mt-8">
                    <button
                        type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 rounded-lg text-base font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1m0-10V5a2 2 0 00-2-2h-3a2 2 0 00-2 2v1" />
                        </svg>
                        {{ form.processing ? 'Logging in...' : 'Login' }}
                    </button>
                </div>
            </form>

            <div class="mt-10 text-center">
                <p class="text-sm text-gray-600">
                    New to Sponsoring24?
                    <a :href="route('register')" class="font-medium text-purple-600 hover:text-purple-500 hover:underline transition-colors duration-200 ml-1">
                        Create an account
                    </a>
                </p>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { route } from '@/ziggy-plugin';
const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onSuccess: () => {
            form.reset('password');
        }
    });
};
</script> 