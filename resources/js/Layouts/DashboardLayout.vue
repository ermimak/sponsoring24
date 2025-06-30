<template>
  <div class="min-h-screen flex bg-gray-50 font-sans">
    <!-- Sidebar -->
    <aside class="w-72 bg-gradient-to-b from-purple-800 to-blue-900 text-white flex flex-col shadow-xl z-20">
      <div class="flex items-center h-20 px-6 font-bold text-2xl tracking-tight border-b border-white/10">
        <div class="bg-gradient-to-r from-purple-400 to-blue-400 text-white rounded-lg p-2 mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-100 to-purple-200 font-extrabold">Sponsoring24</span>
      </div>
      <div class="px-4 py-6">
        <div class="flex items-center space-x-3 bg-white/10 rounded-lg p-3 mb-6">
          <div class="bg-gradient-to-r from-purple-500 to-blue-500 rounded-full p-2 flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="flex flex-col">
            <span class="text-sm font-medium">{{ user.name }}</span>
            <span class="text-xs text-blue-200">{{ user.organization || 'Admin' }}</span>
          </div>
        </div>
      </div>
      <nav class="flex-1 px-4 space-y-1 overflow-y-auto scrollbar-thin scrollbar-thumb-blue-700 scrollbar-track-transparent">
        <SidebarLink icon="users" label="Members" :to="route('dashboard.members.index')" />
        <SidebarLink icon="folder" label="Campaigns" :to="route('dashboard.projects.index')" />
        <!-- <SidebarLink icon="chart-bar" label="Analytics" to="/dashboard/analytics" /> -->
        <SidebarLink icon="adjustments" label="Settings" :to="route('dashboard.settings')" />
        <SidebarLink icon="lock-closed" label="User Management" :to="route('dashboard.users')" />
        <SidebarLink icon="currency-dollar" label="Bonus Credit" :to="route('dashboard.bonus.index')" />
        <SidebarLink icon="currency-dollar" label="Referrals" :to="route('dashboard.referrals')" />
        <SidebarLink icon="currency-dollar" label="License" :to="route('dashboard.license')" />
      </nav>
      <div class="mt-auto p-4 text-xs text-blue-200 border-t border-white/10 flex items-center justify-center">
        <span>© Sponsoring24 2025</span>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Topbar -->
      <header class="h-20 flex items-center justify-between px-8 bg-white shadow-sm border-b border-gray-100">
        <div class="flex items-center space-x-4">
          <slot name="breadcrumbs"></slot>
        </div>
        <div class="flex items-center space-x-4">
          <!-- Notification Dropdown -->
          <NotificationDropdown :notifications="notifications" />
          
          <!-- Language Switcher -->
          <div class="bg-gray-50 rounded-lg flex overflow-hidden border border-gray-200">
            <button 
              v-for="locale in ['de', 'fr', 'it']" 
              :key="locale"
              @click="currentLocale = locale; switchLanguage()"
              class="px-3 py-2 text-sm font-medium transition-colors duration-200"
              :class="currentLocale === locale ? 'bg-gradient-to-r from-purple-600 to-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
            >
              {{ locale.toUpperCase() }}
            </button>
          </div>
          
          <button class="flex items-center bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h2" />
              <path d="M15 3h-6a2 2 0 00-2 2v2h10V5a2 2 0 00-2-2z"/>
            </svg>
            Outbox
          </button>
          
          <button @click="logout" class="flex items-center bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/>
            </svg>
            Logout
          </button>
        </div>
      </header>
      <main class="flex-1 p-8 bg-gray-50 overflow-y-auto">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import SidebarLink from '@/Components/SidebarLink.vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';
import { route } from '@/ziggy-plugin';

const page = usePage();
const user = computed(() => ({
  name: page.props.auth?.user?.name || page.props.auth?.user?.email,
  organization: page.props.auth?.user?.organization,
}));
const notifications = computed(() => page.props.notifications || []);
const currentLocale = ref(page.props.locale || 'de');

function switchLanguage() {
  router.visit(route('language.switch', { locale: currentLocale.value }), { preserveState: true });
}
function logout() {
  router.post('/logout');
}
</script>

<style scoped>
/**** Add creative background gradients, hover, and focus styles here if needed ****/
</style>