<template>
    <DashboardLayout>
      <template #breadcrumbs>
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 font-bold">
          <span v-if="!user">Add User</span>
          <span v-else>Edit User</span>
        </span>
      </template>
      <div class="p-6">
        <div class="max-w-2xl mx-auto bg-white shadow-md rounded-xl p-6 border border-gray-100">
          <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span v-if="!user">Add New User</span>
            <span v-else>Edit User Details</span>
          </h1>
          
          <!-- Error Messages -->
          <div v-if="errors" class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 text-red-700 rounded-lg flex items-start border border-red-100 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <ul class="list-disc list-inside text-sm">
              <li v-for="(error, field) in errors" :key="field">
                <span class="font-semibold">{{ field.replace('_', ' ') }}:</span> {{ error[0] }}
              </li>
            </ul>
          </div>
          
          <!-- Flash Messages -->
          <div v-if="flashMessage.error" class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 text-red-700 rounded-lg flex items-start border border-red-100 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">{{ flashMessage.error }}</span>
          </div>
          
          <div v-if="flashMessage.success" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 rounded-lg flex items-start border border-green-100 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">{{ flashMessage.success }}</span>
          </div>
          
          <form @submit.prevent="submit" class="space-y-6">
            <div class="border-t border-gray-200 mb-6"></div>
            
            <!-- Personal Information Section -->
            <div class="mb-6">
              <h2 class="text-lg font-semibold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Personal Information
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Gender*</label>
                  <select 
                    v-model="form.gender" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200"
                  >
                    <option value="">Please select...</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">First name*</label>
                  <input 
                    v-model="form.first_name" 
                    type="text" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200"
                    placeholder="Enter first name"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Last name*</label>
                  <input 
                    v-model="form.last_name" 
                    type="text" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200"
                    placeholder="Enter last name"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">E-mail*</label>
                  <input 
                    v-model="form.email" 
                    type="email" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200"
                    placeholder="Enter email address"
                  >
                </div>
              </div>
            </div>
            
            <!-- Authentication Section -->
            <div class="mb-6">
              <h2 class="text-lg font-semibold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Authentication
              </h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Password*</label>
                  <input 
                    v-model="form.password" 
                    type="password" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200"
                    placeholder="Enter password"
                  >
                  <p class="text-xs text-gray-500 mt-1">Leave blank if you do not want to change the password.</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Repeat password*</label>
                  <input 
                    v-model="form.password_confirmation" 
                    type="password" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200"
                    placeholder="Confirm password"
                  >
                </div>
                <div class="pt-2">
                  <label class="flex items-center space-x-3 text-sm font-medium text-gray-700 cursor-pointer">
                    <div class="relative inline-block w-10 mr-2 align-middle select-none">
                      <input 
                        v-model="form.two_factor" 
                        type="checkbox" 
                        class="sr-only"
                      />
                      <div class="w-10 h-5 bg-gray-200 rounded-full shadow-inner"></div>
                      <div :class="{'translate-x-5': form.two_factor, 'translate-x-0': !form.two_factor}" class="absolute block w-5 h-5 mt-0 -ml-0.5 -mt-0.5 rounded-full shadow inset-y-0 left-0 focus-within:shadow-outline transition-transform duration-300 ease-in-out bg-white">
                        <input type="checkbox" class="absolute opacity-0 w-0 h-0" />
                      </div>
                    </div>
                    <span>Two-factor authentication</span>
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Roles & Permissions Section -->
            <div class="mb-6">
              <h2 class="text-lg font-semibold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Roles & Permissions
              </h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Roles</label>
                  <select 
                    v-model="form.roles" 
                    multiple 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 min-h-[100px]"
                  >
                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">Hold Ctrl (or Cmd) to select multiple roles</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Permissions</label>
                  <select 
                    v-model="form.permissions" 
                    multiple 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 min-h-[100px]"
                  >
                    <option v-for="permission in permissions" :key="permission.id" :value="permission.id">{{ permission.name }}</option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">Hold Ctrl (or Cmd) to select multiple permissions</p>
                </div>
              </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
              <button 
                type="button" 
                @click="router.get('/dashboard/users')" 
                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 font-medium shadow-sm border border-gray-200 flex items-center"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
              </button>
              <button 
                type="submit" 
                class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-md flex items-center"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save User
              </button>
            </div>
          </form>
        </div>
      </div>
    </DashboardLayout>
  </template>
  
  <script setup>
  import DashboardLayout from '@/Layouts/DashboardLayout.vue';
  import { useForm, usePage, router } from '@inertiajs/vue3';

  // Import the route function
  const route = (name, params, absolute) => {
    return window.route(name, params, absolute);
  };

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
        flashMessage.error = '';
        // The errors will be automatically handled by Inertia's error handling
      },
    });
  }
  </script>