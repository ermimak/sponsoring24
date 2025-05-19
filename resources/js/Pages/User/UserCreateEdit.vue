<template>
    <DashboardLayout>
      <template #breadcrumbs>
        <span v-if="!user">Add additional login</span>
        <span v-else>Edit user</span>
      </template>
      <div class="p-6">
        <div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
          <div v-if="errors" class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <p v-for="(error, index) in Object.values(errors)" :key="index">{{ error }}</p>
          </div>
          <div v-if="flashMessage.error" class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            {{ flashMessage.error }}
          </div>
          <div v-if="flashMessage.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ flashMessage.success }}
          </div>
          <form @submit.prevent="submit" class="space-y-6">
            <div class="border-t border-gray-200"></div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Gender*</label>
                <select v-model="form.gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                  <option value="">Please select...</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">First name*</label>
                <input v-model="form.first_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Last name*</label>
                <input v-model="form.last_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">E-mail*</label>
              <input v-model="form.email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Password*</label>
              <input v-model="form.password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
              <p class="text-xs text-gray-500 mt-1">Leave blank if you do not want to change the password.</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Repeat password*</label>
              <input v-model="form.password_confirmation" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">
                <input v-model="form.two_factor" type="checkbox"> Two-factor authentication
              </label>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Roles</label>
              <select v-model="form.roles" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Permissions</label>
              <select v-model="form.permissions" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option v-for="permission in permissions" :key="permission.id" :value="permission.id">{{ permission.name }}</option>
              </select>
            </div>
            <div class="flex justify-end space-x-4">
              <button type="button" @click="$inertia.get(route('dashboard.users'))" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Save</button>
            </div>
          </form>
        </div>
      </div>
    </DashboardLayout>
  </template>
  
  <script setup>
  import DashboardLayout from '@/Layouts/DashboardLayout.vue';
  import { useForm } from '@inertiajs/vue3';
  import { usePage } from '@inertiajs/vue3';
  
  const page = usePage();
  const props = defineProps({
    roles: Array,
    permissions: Array,
    user: Object,
    errors: Object,
  });
  
  const flashMessage = {
    success: page.props.flash?.success || '',
    error: page.props.flash?.error || '',
  };
  
  const form = useForm({
    user_id: props.user?.id || null,
    gender: props.user?.gender || '',
    first_name: props.user?.name?.split(' ')[0] || '',
    last_name: props.user?.name?.split(' ')[1] || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
    two_factor: props.user?.two_factor_enabled || false,
    roles: props.user?.roles?.map(r => r.id) || [],
    permissions: props.user?.permissions?.map(p => p.id) || [],
  });
  
  function submit() {
    form.post(route('dashboard.users.store'), {
      onSuccess: () => {
        form.reset();
        flashMessage.success = page.props.flash?.success || '';
        flashMessage.error = '';
      },
      onError: (errors) => {
        flashMessage.error = Object.values(errors)[0] || 'An error occurred.';
      },
    });
  }
  </script>