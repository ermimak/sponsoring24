<template>
  <div class="min-h-screen flex bg-gray-50 font-sans">
    <!-- Sidebar -->
    <aside class="w-64 bg-primary text-white flex flex-col shadow-lg z-20">
      <div class="flex items-center h-20 px-6 font-bold text-2xl tracking-tight border-b border-primary-dark">
        <span class="text-accent">◎</span> fundoo
      </div>
      <nav class="flex-1 py-6 px-2 space-y-2">
        <SidebarLink icon="users" label="Members" to="/dashboard/members" />
        <SidebarLink icon="folder" label="Projects" :to="route('dashboard.projects.index')" />
        <SidebarLink icon="adjustments" label="Settings" :to="route('dashboard.settings')" />
        <SidebarLink icon="lock-closed" label="User management" :to="route('dashboard.users')" />
        <SidebarLink icon="currency-dollar" label="Bonus credit" :to="route('dashboard.bonus')" />
      </nav>
      <div class="mt-auto p-4 text-xs text-primary-light">© fundoo 2025</div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Topbar -->
      <header class="h-16 flex items-center justify-between px-8 bg-white shadow border-b border-gray-200">
        <div class="flex items-center space-x-4">
          <slot name="breadcrumbs"></slot>
        </div>
        <div class="flex items-center space-x-6">
          <!-- Language Switcher -->
          <select v-model="currentLocale" @change="switchLanguage" class="rounded bg-gray-100 px-2 py-1 text-primary-dark focus:outline-none">
            <option value="de">DE</option>
            <option value="fr">FR</option>
            <option value="it">IT</option>
          </select>
          <button class="flex items-center text-secondary hover:text-accent transition">
            <svg class="w-5 h-5 mr-1"
fill="none"
stroke="currentColor"
stroke-width="2"
viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h2" /><path d="M15 3h-6a2 2 0 00-2 2v2h10V5a2 2 0 00-2-2z"/></svg>
            Outbox
          </button>
          <button @click="logout" class="flex items-center text-danger hover:text-accent transition">
            <svg class="w-5 h-5 mr-1"
fill="none"
stroke="currentColor"
stroke-width="2"
viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/></svg>
            Logout
          </button>
          <div class="flex items-center space-x-2 bg-primary-light rounded px-3 py-1">
            <span class="font-semibold">{{ user.name }}</span>
            <span class="text-xs text-primary-dark">{{ user.organization }}</span>
          </div>
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
import { route } from '@/ziggy-plugin';

const page = usePage();
const user = computed(() => ({
  name: page.props.auth?.user?.name || page.props.auth?.user?.email,
  organization: page.props.auth?.user?.organization ,
}));
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