<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
            <div class="flex-1 flex flex-col min-h-0 bg-white border-r border-gray-200">
                <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <a href="/" class="text-xl font-bold text-gray-800">Fundoo</a>
                    </div>
                    
                    <!-- Language Switcher -->
                    <div class="px-4 mt-4">
                        <select 
                            :value="locale"
                            @change="switchLanguage"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="de">Deutsch</option>
                            <option value="fr">Français</option>
                        </select>
                    </div>

                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <!-- Common Navigation -->
                        <a
                            href="/dashboard"
                            class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
                            :class="[page.url.startsWith('/dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900']"
                        >
                            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            {{ t('dashboard.home') }}
                        </a>

                        <!-- Project Management -->
                        <template v-if="hasPermission(['view_projects', 'manage_projects'])">
                            <a
                                href="/dashboard/projects"
                                class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
                                :class="[page.url.startsWith('/dashboard/projects') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900']"
                            >
                                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                {{ t('dashboard.projects') }}
                            </a>
                        </template>

                        <!-- Donations -->
                        <template v-if="hasPermission(['view_donations', 'manage_donations'])">
                            <a
                                href="/dashboard/donations"
                                class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
                                :class="[page.url.startsWith('/dashboard/donations') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900']"
                            >
                                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ t('dashboard.donations') }}
                            </a>
                        </template>

                        <!-- Sponsorship Runs -->
                        <template v-if="hasPermission(['view_sponsorship_runs', 'manage_sponsorship_runs'])">
                            <a
                                href="/dashboard/sponsorship-runs"
                                class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
                                :class="[page.url.startsWith('/dashboard/sponsorship-runs') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900']"
                            >
                                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                {{ t('dashboard.sponsorship_runs') }}
                            </a>
                        </template>

                        <!-- Reports & Analytics -->
                        <template v-if="hasPermission(['view_reports', 'manage_reports'])">
                            <a
                                href="/dashboard/reports"
                                class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
                                :class="[page.url.startsWith('/dashboard/reports') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900']"
                            >
                                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                {{ t('dashboard.reports') }}
                            </a>
                        </template>

                        <!-- Admin Section -->
                        <template v-if="hasPermission(['manage_users', 'manage_roles', 'manage_permissions'])">
                            <div class="mt-4 border-t pt-4">
                                <div class="text-xs text-gray-400 uppercase mb-2">{{ t('dashboard.admin') }}</div>
                                
                                <template v-if="hasPermission(['manage_users'])">
                                    <a href="/dashboard/admin/users" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        {{ t('dashboard.manage_users') }}
                                    </a>
                                </template>

                                <template v-if="hasPermission(['manage_roles'])">
                                    <a href="/dashboard/admin/roles" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ t('dashboard.manage_roles') }}
                                    </a>
                                </template>

                                <template v-if="hasPermission(['manage_permissions'])">
                                    <a href="/dashboard/admin/permissions" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m9-6a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ t('dashboard.manage_permissions') }}
                                    </a>
                                </template>

                                <!-- Platform Admin Only -->
                                <template v-if="hasPermission(['manage_platform'])">
                                    <a href="/dashboard/admin/settings" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ t('dashboard.platform_settings') }}
                                    </a>
                                </template>
                            </div>
                        </template>
                    </nav>
                </div>

                <!-- User Profile Section -->
                <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                    <div class="flex-shrink-0 w-full group block">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div>
                                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-gray-500">
                                        <span class="text-sm font-medium leading-none text-white">
                                            {{ page.props?.auth?.user?.name ? page.props.auth.user.name.charAt(0) : '?' }}
                                        </span>
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                                        {{ page.props?.auth?.user?.name || 'Guest' }}
                                    </p>
                                    <p class="text-xs font-medium text-gray-500 group-hover:text-gray-700">
                                        {{ page.props?.auth?.user?.email || '' }}
                                    </p>
                                </div>
                            </div>
                            <button 
                                @click="logout" 
                                type="button" 
                                class="text-sm text-gray-500 hover:text-gray-700"
                            >
                                {{ t('auth.logout') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="md:pl-64 flex flex-col flex-1">
            <main class="flex-1">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <slot></slot>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { route } from '../ziggy'

const page = usePage()
const { t, locale } = useI18n()

// Set initial locale from page props
onMounted(() => {
    if (page.props?.locale) {
        locale.value = page.props.locale
    }
})

const hasPermission = (permissions) => {
    const userPermissions = page.props?.auth?.user?.permissions || []
    return permissions.some(permission => userPermissions.includes(permission))
}

const switchLanguage = (event) => {
    const newLocale = event.target.value
    router.visit(route('language.switch', { locale: newLocale }), {
        preserveState: true,
        preserveScroll: true,
        only: ['locale']
    })
}

const logout = () => {
    router.post('/logout', {}, {
        preserveScroll: true,
        onSuccess: () => {
            window.location.href = '/'
        }
    })
}
</script> 