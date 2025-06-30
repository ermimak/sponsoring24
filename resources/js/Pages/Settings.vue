<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
      <!-- Page Title -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Account Settings</h1>
        <p class="text-gray-600 mt-2">Manage your organization profile, appearance, and account preferences</p>
      </div>

      <!-- Profile Section -->
      <div class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold flex items-center text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span>Profile Information</span>
          <button @click="toggleSection('profile')" class="ml-auto text-gray-500 hover:text-purple-600 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.profile" @submit.prevent="saveSettings" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Organization name</label>
              <input 
                v-model="form.organization_name" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="Your organization name"
              />
              <p v-if="errors.organization_name" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.organization_name }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Contact title</label>
              <select 
                v-model="form.contact_title" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 bg-white"
              >
                <option value="Mister">Mister</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
              </select>
              <p v-if="errors.contact_title" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.contact_title }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Contact person first name*</label>
              <input 
                v-model="form.contact_first_name" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="First name"
                required
              />
              <p v-if="errors.contact_first_name" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.contact_first_name }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Contact person last name*</label>
              <input 
                v-model="form.contact_last_name" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="Last name"
                required
              />
              <p v-if="errors.contact_last_name" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.contact_last_name }}
              </p>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Address*</label>
              <input 
                v-model="form.address" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="Street address"
                required
              />
              <p v-if="errors.address" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.address }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Address suffix</label>
              <input 
                v-model="form.address_suffix" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="Apartment, suite, etc."
              />
              <p v-if="errors.address_suffix" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.address_suffix }}
              </p>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Postal code*</label>
              <input 
                v-model="form.postal_code" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="Postal code"
                required
              />
              <p v-if="errors.postal_code" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.postal_code }}
              </p>
            </div>
            <div class="space-y-2 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Location*</label>
              <input 
                v-model="form.location" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="City"
                required
              />
              <p v-if="errors.location" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.location }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Country*</label>
              <input 
                v-model="form.country" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="Country"
                required
              />
              <p v-if="errors.country" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.country }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Language*</label>
              <select 
                v-model="form.language" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 bg-white" 
                required
              >
                <option value="German">German</option>
                <option value="English">English</option>
                <option value="French">French</option>
                <option value="Italian">Italian</option>
              </select>
              <p v-if="errors.language" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.language }}
              </p>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">E-mail*</label>
              <input 
                v-model="form.email" 
                type="email" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="your@email.com"
                required 
              />
              <p v-if="errors.email" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.email }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Phone*</label>
              <input 
                v-model="form.phone" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="+41 123 456 789"
                required 
              />
              <p v-if="errors.phone" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.phone }}
              </p>
            </div>
          </div>
          <div v-if="form.user">
            <label class="block text-sm font-medium text-gray-700">Managed by</label>
            <p class="text-sm">{{ form.user.name }} ({{ form.user.email }})</p>
          </div>
        </form>
      </div>

      <!-- Appearance Section -->
      <div class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold flex items-center text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
          </svg>
          <span>Brand Appearance</span>
          <button @click="toggleSection('appearance')" class="ml-auto text-gray-500 hover:text-purple-600 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.appearance" @submit.prevent="saveSettings" class="space-y-6">
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Accent color</label>
            <div class="flex items-center space-x-3">
              <input 
                v-model="form.accent_color" 
                type="color" 
                class="h-12 w-12 rounded-lg shadow-sm border-2 border-gray-300 cursor-pointer" 
              />
              <input 
                v-model="form.accent_color" 
                type="text" 
                class="w-40 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="#9500FF"
              />
              <div class="ml-2 text-sm text-gray-500">Sponsoring24 default: #9500FF</div>
            </div>
            <p v-if="errors.accent_color" class="text-red-500 text-sm mt-1 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              {{ errors.accent_color }}
            </p>
          </div>
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Logo</label>
            <div class="flex items-center space-x-4">
              <div class="h-20 w-20 flex items-center justify-center rounded-lg border-2 border-gray-300 bg-gray-50">
                <img v-if="form.logo_path" :src="form.logo_path" alt="Logo" class="h-16 w-16 object-contain" />
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="flex flex-col space-y-2">
                <input type="file" @change="handleLogoUpload" accept="image/*" class="hidden" id="logo-upload" />
                <label for="logo-upload" class="cursor-pointer bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-4 py-2 rounded-lg shadow-sm inline-flex items-center transition duration-200">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                  </svg>
                  Upload logo
                </label>
                <p class="text-sm text-gray-500">Recommended size: 200x200px, PNG or SVG</p>
              </div>
            </div>
            <p v-if="errors.logo" class="text-red-500 text-sm mt-1 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              {{ errors.logo }}
            </p>
          </div>
          
          <div class="space-y-3 mt-6">
            <h3 class="text-md font-medium text-gray-700 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Brand Preview
            </h3>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
              <div class="flex flex-wrap gap-3 items-center">
                <button :style="{ backgroundColor: form.accent_color }" class="px-4 py-2 rounded-lg text-white shadow-sm font-medium transition duration-200 hover:opacity-90">Primary Button</button>
                <button :style="{ color: form.accent_color, border: '1px solid ' + form.accent_color }" class="px-4 py-2 rounded-lg bg-white shadow-sm font-medium transition duration-200 hover:bg-gray-50">Secondary Button</button>
                <span :style="{ color: form.accent_color }" class="font-medium cursor-pointer hover:underline">Text Link</span>
                <div :style="{ backgroundColor: form.accent_color }" class="h-6 w-6 rounded-full shadow-sm"></div>
                <div :style="{ backgroundColor: form.accent_color + '33' }" class="h-6 w-6 rounded-full shadow-sm"></div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Billing Address Section -->
      <div class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold flex items-center text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
          </svg>
          <span>Billing Address</span>
          <button @click="toggleSection('billing')" class="ml-auto text-gray-500 hover:text-purple-600 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.billing" @submit.prevent="saveSettings" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Salutation</label>
              <select 
                v-model="form.billing_salutation" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 bg-white"
              >
                <option value="Mister">Mister</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
              </select>
              <p v-if="errors.billing_salutation" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_salutation }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">First name</label>
              <input 
                v-model="form.billing_first_name" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="First name"
              />
              <p v-if="errors.billing_first_name" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_first_name }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Last name</label>
              <input 
                v-model="form.billing_last_name" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="Last name"
              />
              <p v-if="errors.billing_last_name" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_last_name }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Address suffix</label>
              <input 
                v-model="form.billing_address_suffix" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="c/o, Apartment, etc."
              />
              <p v-if="errors.billing_address_suffix" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_address_suffix }}
              </p>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Address</label>
              <input 
                v-model="form.billing_address" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="Street address"
              />
              <p v-if="errors.billing_address" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_address }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Postal code</label>
              <input 
                v-model="form.billing_postal_code" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="ZIP/Postal code"
              />
              <p v-if="errors.billing_postal_code" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_postal_code }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Location</label>
              <input 
                v-model="form.billing_location" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="City"
              />
              <p v-if="errors.billing_location" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_location }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Country</label>
              <select 
                v-model="form.billing_country" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 bg-white"
              >
                <option value="Switzerland">Switzerland</option>
                <option value="Germany">Germany</option>
                <option value="Austria">Austria</option>
                <option value="France">France</option>
                <option value="Italy">Italy</option>
              </select>
              <p v-if="errors.billing_country" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_country }}
              </p>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">E-mail</label>
              <input 
                v-model="form.billing_email" 
                type="email" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="your@email.com"
              />
              <p v-if="errors.billing_email" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_email }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Phone</label>
              <input 
                v-model="form.billing_phone" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="+41 123 456 789"
              />
              <p v-if="errors.billing_phone" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.billing_phone }}
              </p>
            </div>
          </div>
        </form>
      </div>

      <!-- Bank Details Section -->
      <div class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold flex items-center text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
          </svg>
          <span>Bank Details</span>
          <button @click="toggleSection('bank')" class="ml-auto text-gray-500 hover:text-purple-600 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.bank" @submit.prevent="saveSettings" class="space-y-6">
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Bank account for transferring donations</label>
            <textarea 
              v-model="form.bank_account_details" 
              class="w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
              rows="3"
              placeholder="Enter your bank account details here"
            ></textarea>
            <p v-if="errors.bank_account_details" class="text-red-500 text-sm mt-1 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              {{ errors.bank_account_details }}
            </p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">IBAN account number</label>
              <input 
                v-model="form.iban" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="CH00 0000 0000 0000 0000 0"
              />
              <p v-if="errors.iban" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.iban }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Recipient</label>
              <input 
                v-model="form.recipient" 
                type="text" 
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                placeholder="Account holder name"
              />
              <p v-if="errors.recipient" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.recipient }}
              </p>
            </div>
          </div>
        </form>
      </div>

      <!-- Reset Password Section -->
      <div class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold flex items-center text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <span>Reset Password</span>
          <button @click="toggleSection('password')" class="ml-auto text-gray-500 hover:text-purple-600 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.password" @submit.prevent="saveSettings" class="space-y-6">
          <div class="p-4 bg-purple-50 rounded-lg border border-purple-100 mb-2">
            <p class="text-sm text-gray-700">
              <span class="font-medium">Password requirements:</span> Leave these fields blank if you do not want to change the password. The password must be at least 8 characters long, contain one uppercase and one lowercase letter, and one number.
            </p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">New Password</label>
              <div class="relative">
                <input 
                  v-model="form.password" 
                  type="password" 
                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                  placeholder="••••••••"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </div>
              </div>
              <p v-if="errors.password" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.password }}
              </p>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
              <div class="relative">
                <input 
                  v-model="form.password_confirmation" 
                  type="password" 
                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                  placeholder="••••••••"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </div>
              </div>
              <p v-if="errors.password_confirmation" class="text-red-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.password_confirmation }}
              </p>
            </div>
          </div>
        </form>
      </div>

      <!-- Referral Program Section -->
      <div class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold flex items-center text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
          </svg>
          <span>Referral Program</span>
          <button @click="toggleSection('referral')" class="ml-auto text-gray-500 hover:text-purple-600 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <div v-if="activeSections.referral" class="space-y-6">
          <div class="p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-100 mb-2">
            <p class="text-sm text-gray-700">
              <span class="font-medium">Share your referral link:</span> When someone signs up using your link and purchases an annual license, you'll earn CHF 100 in bonus credits, and they'll get CHF 50 off their first annual license.
            </p>
          </div>
          
          <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Your Referral Link</label>
            <div class="flex items-center">
              <input 
                type="text" 
                readonly 
                :value="referralLink" 
                class="flex-grow px-4 py-2.5 rounded-l-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 bg-gray-50" 
              />
              <button 
                @click="copyReferralLink" 
                type="button"
                class="px-4 py-2.5 bg-purple-600 text-white rounded-r-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors duration-200 flex items-center"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>
                Copy
              </button>
            </div>
            <p v-if="linkCopied" class="text-green-600 text-sm mt-1 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Link copied to clipboard!
            </p>
          </div>
          
          <div class="space-y-3 mt-4">
            <label class="block text-sm font-medium text-gray-700">Share on Social Media</label>
            <div class="flex flex-wrap gap-3">
              <a 
                :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(referralLink)}`"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                </svg>
                Facebook
              </a>
              <a 
                :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(referralLink)}&text=${encodeURIComponent('Join Fundoo and get CHF 50 off your first annual license!')}`"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-blue-400 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-600 focus:outline-none focus:border-blue-600 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                </svg>
                Twitter
              </a>
              <a 
                :href="`mailto:?subject=${encodeURIComponent('Join Fundoo and save CHF 50')}&body=${encodeURIComponent('I thought you might be interested in Fundoo. Sign up using my referral link and get CHF 50 off your first annual license: ' + referralLink)}`"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Email
              </a>
            </div>
          </div>
          
          <div class="mt-6">
            <a 
              :href="route('dashboard.referrals')" 
              class="inline-flex items-center px-4 py-2 bg-purple-100 border border-transparent rounded-md font-medium text-sm text-purple-700 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              View Referral Statistics
            </a>
          </div>
        </div>
      </div>
      
      <!-- Project Overview Section -->
      <div class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold flex items-center text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
          </svg>
          <span>Project Overview</span>
          <button @click="toggleSection('overview')" class="ml-auto text-gray-500 hover:text-purple-600 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.overview" @submit.prevent="saveSettings" class="space-y-4">
          <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
              <input 
                v-model="form.project_overview_enabled"
                type="checkbox"
                id="project_overview"
                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 border-gray-300 appearance-none cursor-pointer transition-transform duration-200 ease-in-out checked:right-0 checked:border-purple-500"
              />
              <label for="project_overview" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
            </div>
            <label for="project_overview" class="text-sm font-medium text-gray-700 cursor-pointer">Activate project overview</label>
            <p v-if="errors.project_overview_enabled" class="text-red-500 text-sm mt-1 ml-2 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              {{ errors.project_overview_enabled }}
            </p>
          </div>
        </form>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end mt-8">
        <button 
          type="submit" 
          @click.prevent="saveSettings" 
          class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg shadow-md font-medium transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 flex items-center"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Save Changes
        </button>
      </div>

      <!-- Flash Messages -->
      <div v-if="flashMessage.success" class="fixed bottom-4 right-4 bg-gradient-to-r from-emerald-500 to-green-500 text-white px-6 py-3 rounded-lg shadow-xl flex items-center max-w-md animate-fade-in-up">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ flashMessage.success }}
      </div>
      <div v-if="flashMessage.error" class="fixed bottom-4 right-4 bg-gradient-to-r from-red-500 to-pink-500 text-white px-6 py-3 rounded-lg shadow-xl flex items-center max-w-md animate-fade-in-up">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ flashMessage.error }}
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import {route} from 'ziggy-js';
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  settings: Object,
})

const page = usePage()
const toast = useToast()

// Debug auth data to console
console.log('Auth data in Settings.vue:', page.props.auth)

// Ensure user data is available with fallbacks
const user = computed(() => {
  const authUser = page.props.auth?.user || {}
  return {
    id: authUser.id || '',
    name: authUser.name || '',
    email: authUser.email || '',
    organization: authUser.organization || ''
  }
})

const referralLink = computed(() => {
  return user.value.id ? `${window.location.origin}/register?ref=${user.value.id}` : ''
})

const linkCopied = ref(false)

const copyReferralLink = () => {
  navigator.clipboard.writeText(referralLink.value)
    .then(() => {
      linkCopied.value = true
      setTimeout(() => {
        linkCopied.value = false
      }, 3000)
    })
    .catch(err => {
      console.error('Failed to copy link: ', err)
    })
}

const form = ref({
  organization_name: props.settings?.organization_name || '',
  contact_title: props.settings?.contact_title || 'Mister',
  contact_first_name: props.settings?.contact_first_name || '',
  contact_last_name: props.settings?.contact_last_name || '',
  address: props.settings?.address || '',
  address_suffix: props.settings?.address_suffix || '',
  postal_code: props.settings?.postal_code || '',
  location: props.settings?.location || '',
  country: props.settings?.country || 'Switzerland',
  language: props.settings?.language || 'German',
  email: props.settings?.email || '',
  phone: props.settings?.phone || '',
  accent_color: props.settings?.accent_color || '#9500FF',
  logo_path: props.settings?.logo_path || '',
  logo: null, // For file upload
  billing_salutation: props.settings?.billing_salutation || 'Mister',
  billing_first_name: props.settings?.billing_first_name || '',
  billing_last_name: props.settings?.billing_last_name || '',
  billing_address: props.settings?.billing_address || '',
  billing_address_suffix: props.settings?.billing_address_suffix || '',
  billing_postal_code: props.settings?.billing_postal_code || '',
  billing_location: props.settings?.billing_location || '',
  billing_country: props.settings?.billing_country || 'Switzerland',
  billing_email: props.settings?.billing_email || '',
  billing_phone: props.settings?.billing_phone || '',
  bank_account_details: props.settings?.bank_account_details || '',
  iban: props.settings?.iban || '',
  recipient: props.settings?.recipient || '',
  project_overview_enabled: props.settings?.project_overview_enabled || false,
  password: '',
  password_confirmation: '',
  user: props.settings?.user || null,
})

const activeSections = ref({
  profile: true,
  appearance: true,
  billing: true,
  bank: true,
  password: true,
  referral: false,
  overview: true,
})

const errors = ref({})
const flashMessage = computed(() => ({
  success: usePage().props.flash?.success || '',
  error: usePage().props.flash?.error || '',
}))

function toggleSection(section) {
  activeSections.value[section] = !activeSections.value[section]
}

function handleLogoUpload(event) {
  const file = event.target.files[0]
  if (file) {
    form.value.logo = file
  }
}

function saveSettings() {
  errors.value = {} // Reset errors
  console.log('Submitting settings form:', form.value)

  const formData = new FormData()
  for (const key in form.value) {
    if (key === 'logo' && form.value[key]) {
      formData.append('logo', form.value[key])
    } else if (key === 'project_overview_enabled') {
      // Ensure boolean is properly converted
      formData.append(key, form.value[key] === true ? '1' : '0')
    } else if (key !== 'user') {
      formData.append(key, form.value[key] ?? '') // Handle null/undefined values
    }
  }

  router.post('/dashboard/settings', formData, {
    onSuccess: (page) => {
      errors.value = {} // Clear errors on success
      form.value.password = '' // Reset password fields
      form.value.password_confirmation = ''
      
      // Update form with new settings if available
      if (page.props.settings) {
        for (const key in page.props.settings) {
          if (form.value.hasOwnProperty(key)) {
            form.value[key] = page.props.settings[key]
          }
        }
      }
      
      // Log success and updated user data
      console.log('Settings updated successfully')
      console.log('Updated settings data:', page.props.settings)
      
      // Show success message
      toast.success('Settings updated successfully')
    },
    onError: (err) => {
      errors.value = err // Set validation errors
      console.error('Settings update failed:', err)
      toast.error('Failed to update settings')
    },
    preserveState: true, // Preserve form state to show errors
    preserveScroll: true, // Keep scroll position
  })
}

watch(() => props.settings, (newSettings) => {
  if (newSettings) {
    for (const key in newSettings) {
      if (form.value.hasOwnProperty(key)) {
        form.value[key] = newSettings[key] ?? form.value[key]
      }
    }
  }
}, { deep: true })
</script>

<style scoped>
.input {
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  padding: 0.5rem 0.75rem;
  width: 100%;
}

.input:focus {
  outline: none;
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
  --tw-ring-color: rgba(168, 85, 247, 0.5);
}

/* Custom toggle switch styles */
.toggle-checkbox:checked {
  right: 0;
  border-color: #a855f7;
}

.toggle-checkbox:checked + .toggle-label {
  background-color: #a855f7;
}

.toggle-label {
  transition: background-color 0.2s ease;
}

/* Animation for flash messages */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out forwards;
}
</style>