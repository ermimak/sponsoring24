<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/" class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-500 hover:from-purple-700 hover:to-blue-600 transition-all duration-300">Sponsoring24</a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex space-x-4">
                            <a href="/de" class="text-gray-600 hover:text-gray-900">Deutsch</a>
                            <a href="/fr" class="text-gray-600 hover:text-gray-900">Français</a>
                            <template v-if="$page.props.auth?.user">
                                <div class="relative ml-3">
                                    <button
                                        type="button"
                                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300"
                                    >
                                        <span class="sr-only">Open user menu</span>
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-500">
                                            <span class="text-sm font-medium leading-none text-white">
                                                {{ $page.props.auth.user.name?.charAt(0) || '?' }}
                                            </span>
                                        </span>
                                    </button>
                                </div>
                            </template>
                            <template v-else>
                                <a href="/login" class="text-gray-600 hover:text-gray-900">Login</a>
                                <a href="/register" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                                    Register
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <slot></slot>
        </main>

        <footer class="bg-gray-800 text-white mt-12">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Sponsoring24</h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <Link :href="route('about')" class="text-base text-gray-300 hover:text-white">About Us</Link>
                            </li>
                            <li>
                                <Link :href="route('contact')" class="text-base text-gray-300 hover:text-white">Contact</Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Legal</h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <Link :href="route('terms')" class="text-base text-gray-300 hover:text-white">Terms of Service</Link>
                            </li>
                            <li>
                                <Link :href="route('privacy')" class="text-base text-gray-300 hover:text-white">Privacy Policy</Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 pt-8">
                    <p class="text-base text-gray-400 text-center">&copy; {{ new Date().getFullYear() }} Sponsoring24. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3';

const page = usePage();
</script> 