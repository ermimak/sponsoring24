<template>
    <DashboardLayout>
      <template #breadcrumbs>
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 font-bold">User Management</span>
      </template>
      <div class="p-6">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            User Management
          </h1>
          <button 
            @click="$inertia.get(route('dashboard.users.create'))" 
            class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-5 py-2.5 rounded-lg hover:from-purple-700 hover:to-blue-700 shadow-md transition-all duration-200 flex items-center font-medium"
          >
            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Create User
          </button>
        </div>
        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
          <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gradient-to-r from-purple-600 to-blue-600">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Name</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">E-mail</th>
                  <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                <tr v-for="user in users" :key="user.id" class="hover:bg-purple-50 transition-colors duration-200 group">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900 group-hover:text-purple-700 transition-colors duration-200">{{ user.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-gray-700 group-hover:text-purple-600 transition-colors duration-200">{{ user.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <button 
                      @click="$inertia.get(route('dashboard.users.edit', user.id))" 
                      class="inline-flex items-center justify-center p-2 rounded-full bg-purple-100 text-purple-600 hover:bg-purple-200 hover:text-purple-800 transition-colors duration-200 mr-2"
                      title="Edit User"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button 
                      @click="confirmDelete(user)" 
                      class="inline-flex items-center justify-center p-2 rounded-full bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-800 transition-colors duration-200"
                      title="Delete User"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </DashboardLayout>
  </template>
  
  <script setup>
  import DashboardLayout from '@/Layouts/DashboardLayout.vue';
  import { usePage, router } from '@inertiajs/vue3';
  import { computed } from 'vue';

  // Import the route function
  const route = (name, params, absolute) => {
    return window.route(name, params, absolute);
  };

  const page = usePage();
  const users = page.props.users;

  const confirmDelete = (user) => {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
      router.delete(`/dashboard/users/${user.id}`, {
        onSuccess: () => {
          // The page will automatically refresh after successful deletion
        },
      });
    }
  };
  </script>