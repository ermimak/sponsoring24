<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-gradient-to-r from-purple-600 to-blue-500 border-b border-purple-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('admin.dashboard')" class="text-white font-extrabold text-xl">
                                <span class="text-2xl font-extrabold text-white">Sponsoring24</span>
                                <span class="ml-2 text-white text-lg">Admin</span>
                            </Link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink :href="route('admin.dashboard')" :active="routeIs('Admin/Dashboard')">
                                Dashboard
                            </NavLink>
                            <NavLink :href="route('admin.users.index')" :active="routeIs('Admin/Users')">
                                Users
                            </NavLink>
                            <NavLink :href="route('admin.content.index')" :active="routeIs('Admin/Content')">
                                Content
                            </NavLink>
                            <NavLink :href="route('dashboard')" :active="false">
                                Back to App
                            </NavLink>
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150">
                                            {{ $page.props.auth?.user?.name || 'User' }}
                                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <form @submit.prevent="logout" class="w-full">
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                            Log Out
                                        </button>
                                    </form>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-white hover:bg-blue-600 focus:outline-none focus:bg-blue-600 focus:text-white transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <ResponsiveNavLink :href="route('admin.dashboard')" :active="routeIs('Admin/Dashboard')">
                        Dashboard
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('admin.users.index')" :active="routeIs('Admin/Users')">
                        Users
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('admin.content.index')" :active="routeIs('Admin/Content')">
                        Content
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('dashboard')" :active="false">
                        Back to App
                    </ResponsiveNavLink>
                </div>
                <div class="pt-4 pb-1 border-t border-blue-600">
                    <div class="px-4">
                        <div class="font-medium text-base text-white">{{ $page.props.auth?.user?.name || 'User' }}</div>
                        <div class="font-medium text-sm text-blue-100">{{ $page.props.auth?.user?.email || '' }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <header v-if="$slots.header" class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { route } from '@/ziggy-plugin';

// Helper function to check if current route matches a pattern
const routeIs = (name) => {
    const currentComponent = usePage().component.value;
    return currentComponent && currentComponent.includes(name);
};

// Logout function
const logout = () => {
    router.post(route('logout'));
};

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
</script>
