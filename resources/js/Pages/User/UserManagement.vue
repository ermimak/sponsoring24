<template>
    <DashboardLayout>
      <template #breadcrumbs>
        <span>User management</span>
      </template>
      <div class="p-6">
        <div class="flex justify-end mb-4">
          <button @click="$inertia.get(route('dashboard.users.create'))" class="bg-yellow-400 text-white px-4 py-2 rounded-full hover:bg-yellow-500">
            <svg class="w-5 h-5 inline-block"
fill="none"
stroke="currentColor"
viewBox="0 0 24 24">
              <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Create
          </button>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
          <table class="w-full">
            <thead>
              <tr class="border-b">
                <th class="py-2 px-4 text-left">Name</th>
                <th class="py-2 px-4 text-left">E-mail</th>
                <th class="py-2 px-4 text-left">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" class="border-b">
                <td class="py-2 px-4">{{ user.name }}</td>
                <td class="py-2 px-4">{{ user.email }}</td>
                <td class="py-2 px-4">
                  <button @click="$inertia.get(route('dashboard.users.edit', user.id))" class="text-purple-500 hover:text-purple-700 mr-2">
                    <svg class="w-5 h-5"
fill="none"
stroke="currentColor"
viewBox="0 0 24 24">
                      <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M11 5H6a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button @click="confirmDelete(user)" class="text-purple-500 hover:text-purple-700">
                    <svg class="w-5 h-5"
fill="none"
stroke="currentColor"
viewBox="0 0 24 24">
                      <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </DashboardLayout>
  </template>
  
  <script setup>
  import DashboardLayout from '@/Layouts/DashboardLayout.vue';
  import { usePage } from '@inertiajs/vue3';
  import { router } from '@inertiajs/vue3';
  
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