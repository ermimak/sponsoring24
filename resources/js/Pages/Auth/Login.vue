<template>
    <AuthLayout>
        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="email" class="block text-lg font-bold text-gray-700 mb-1">
                    Login
                </label>
                <div class="flex items-center gap-2">
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        placeholder="E-Mail-Adresse"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base"
                    />
                    <span class="text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg"
class="h-5 w-5"
fill="none"
viewBox="0 0 24 24"
stroke="currentColor"><path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M16 12a4 4 0 01-8 0m8 0a4 4 0 00-8 0m8 0V8a4 4 0 00-8 0v4m8 0v4a4 4 0 01-8 0v-4" /></svg>
                    </span>
                </div>
                <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                    {{ form.errors.email }}
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        placeholder="Passwort"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base"
                    />
                    <span class="text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg"
class="h-5 w-5"
fill="none"
viewBox="0 0 24 24"
stroke="currentColor"><path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-.896 2-2 2-2-.896-2-2zm0 0V7m0 4v4" /></svg>
                    </span>
                </div>
                <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                    {{ form.errors.password }}
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember_me"
                        v-model="form.remember"
                        type="checkbox"
                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                    />
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Stay logged in
                    </label>
                </div>
                <div class="text-sm">
                    <a href="/forgot-password" class="text-purple-600 hover:underline">
                        Forgot your password?
                    </a>
                </div>
            </div>
            <div>
                <button
                    type="submit"
                    class="w-full flex justify-center py-3 px-4 rounded-full text-lg font-bold text-white bg-purple-600 hover:bg-purple-700 transition-colors shadow-md"
                    :disabled="form.processing"
                >
                    Login
                </button>
            </div>
        </form>
        <div class="mt-8 text-center">
            <div class="text-xl font-bold text-gray-700 mb-2">
                New to fundoo as an organizer?
            </div>
            <a href="/register" class="text-purple-700 underline font-medium text-base">
                Register now and start your project
            </a>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post('/login', {
        onSuccess: () => {
            form.reset('password');
        }
    });
};
</script> 