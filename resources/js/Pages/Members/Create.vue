<template>
  <DashboardLayout>
    <div>
      <h1 class="text-3xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
        <svg v-if="!editingId" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
        {{ editingId ? 'Edit Member' : 'Add Member' }}
      </h1>
      <form @submit.prevent="submit" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Profile -->
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm col-span-2">
            <h2 class="font-semibold text-lg mb-5 flex items-center pb-2 border-b border-gray-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Profile Information</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Gender{{ editingId ? '' : '*' }}
                </label>
                <select 
                  v-model="form.gender" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                  :required="!editingId"
                >
                  <option value="">Please select...</option>
                  <option value="female">Female</option>
                  <option value="male">Male</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  Company
                </label>
                <input 
                  v-model="form.company" 
                  type="text" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                  placeholder="Company name"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  First Name{{ editingId ? '' : '*' }}
                </label>
                <input 
                  v-model="form.first_name"
                  type="text"
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                  :required="!editingId" 
                  placeholder="First name"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Last Name{{ editingId ? '' : '*' }}
                </label>
                <input 
                  v-model="form.last_name"
                  type="text"
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                  :required="!editingId" 
                  placeholder="Last name"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                  </svg>
                  Address
                </label>
                <input 
                  v-model="form.address" 
                  type="text" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                  placeholder="Street address"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                  </svg>
                  Address Suffix
                </label>
                <input 
                  v-model="form.address_suffix" 
                  type="text" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                  placeholder="Apartment, suite, etc."
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  Postal Code
                </label>
                <input 
                  v-model="form.postal_code" 
                  type="text" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                  placeholder="Postal code"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  Location
                </label>
                <input 
                  v-model="form.location" 
                  type="text" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                  placeholder="City"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Country
                </label>
                <input 
                  v-model="form.country" 
                  type="text" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                  placeholder="Country"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  Birthday
                </label>
                <input 
                  v-model="form.birthday" 
                  type="date" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Email{{ editingId ? '' : '*' }}
                </label>
                <input 
                  v-model="form.email"
                  type="email"
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                  :required="!editingId" 
                  placeholder="Email address"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                  </svg>
                  Member ID
                </label>
                <input 
                  v-model="form.member_id" 
                  type="text" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                  placeholder="Unique member ID"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Joined Date
                </label>
                <input 
                  v-model="form.joined_date" 
                  type="date" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                />
              </div>
            </div>
            <div class="mb-2">
              <label class="block text-sm font-medium mb-1">Email CC</label>
              <input v-model="form.email_cc" type="email" class="input w-full" />
            </div>
            <div class="mb-2">
              <label class="block text-sm font-medium mb-1">Phone</label>
              <input v-model="form.phone" type="text" class="input w-full" />
            </div>
          </div>
          <!-- Contact & Groups -->
          <div class="flex flex-col gap-6">
            <!-- Contact section removed as it's now part of the profile section -->
            
            <!-- Member Groups -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
              <h2 class="font-semibold text-lg mb-5 flex items-center pb-2 border-b border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Member Groups</span>
              </h2>
              <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Assign to Groups
              </label>
              <select 
                v-model="form.groups" 
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                multiple
              >
                <option v-for="group in allGroups" :key="group.id" :value="group.name">{{ group.name }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Select one or more groups (Ctrl+click or Cmd+click for multiple)
              </p>
            </div>
            
            <!-- Settings -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
              <h2 class="font-semibold text-lg mb-5 flex items-center pb-2 border-b border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Member Settings</span>
              </h2>
              <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="form.archived" 
                  class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 focus:ring-opacity-50" 
                />
                <span class="ml-2 text-sm font-medium text-gray-700 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                  </svg>
                  Archive this member
                </span>
              </label>
              <p class="text-xs text-gray-500 mt-2 ml-6">Archived members won't appear in the main members list</p>
            </div>
          </div>
        </div>
        <!-- Error and loading states -->
        <div v-if="error" class="text-red-600 mb-4 p-3 bg-red-50 rounded-lg flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ error }}
        </div>
        <div v-if="loading" class="text-purple-600 mb-4 p-3 bg-purple-50 rounded-lg flex items-center">
          <svg class="animate-spin h-5 w-5 mr-2 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Loading member data...
        </div>
        
        <!-- Action buttons -->
        <div class="flex flex-wrap gap-4 mt-8">
          <button 
            type="submit" 
            :disabled="loading" 
            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-200 flex items-center disabled:opacity-70 disabled:cursor-not-allowed"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ loading ? 'Saving...' : 'Save' }}
          </button>
          
          <button 
            type="button"
            @click="saveAndClose"
            :disabled="loading"
            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-200 flex items-center disabled:opacity-70 disabled:cursor-not-allowed"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9l-2 2m0 0l-2-2m2 2V5" />
            </svg>
            {{ loading ? 'Saving...' : 'Save and Close' }}
          </button>
          
          <button 
            type="button" 
            @click="cancel" 
            class="px-6 py-2.5 rounded-lg bg-white border border-gray-300 text-gray-700 font-medium shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-200 flex items-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  routeParams: {
    type: Object,
    default: () => ({}),
  },
});

const editingId = ref(null);
const allGroups = ref([]);
const form = ref({
  gender: '',
  company: '',
  first_name: '',
  last_name: '',
  address: '',
  address_suffix: '',
  postal_code: '',
  location: '',
  country: '',
  birthday: '',
  member_id: '',
  email: '',
  email_cc: '',
  phone: '',
  groups: [],
  archived: false,
});
const error = ref('');
const loading = ref(false);

async function fetchGroups() {
  try {
    const response = await axios.get('/dashboard/members/groups/data');
    allGroups.value = response.data.map(g => ({ id: g.id, name: g.name }));
  } catch (e) {
    error.value = 'Failed to load groups. Please try again.';
    allGroups.value = [];
  }
}

async function fetchMember() {
  if (!editingId.value || isNaN(editingId.value)) {
    error.value = 'Invalid member ID. Please select a valid member to edit.';
    return;
  }
  try {
    loading.value = true;
    const response = await axios.get(`/dashboard/members/${editingId.value}`);
    const member = response.data;
    if (!member.id) {
      error.value = 'Member not found. Please select a valid member.';
      return;
    }
    form.value = {
      gender: member.gender || '',
      first_name: member.first_name || '',
      last_name: member.last_name || '',
      company: member.company || '',
      address: member.address || '',
      address_suffix: member.address_suffix || '',
      postal_code: member.postal_code || '',
      location: member.location || '',
      country: member.country || '',
      birthday: member.birthday || '',
      member_id: member.member_id || '',
      email: member.email || '',
      email_cc: member.email_cc || '',
      phone: member.phone || '',
      groups: Array.isArray(member.member_groups) ? member.member_groups.map(g => g.name) : [],
      archived: !!member.archived,
    };
    console.log('Fetched member data:', member);
  } catch (e) {
    console.error('Failed to fetch member:', e.response?.data || e.message);
    error.value = e.response?.status === 404 
      ? 'Member not found. Please select a valid member.'
      : e.response?.data?.message || 'Failed to load member data. Please try again.';
  } finally {
    loading.value = false;
  }
}

async function submit() {
  loading.value = true;
  error.value = '';
  try {
    const payload = {
      ...form.value,
      groups: Array.isArray(form.value.groups) ? form.value.groups : [],
    };
    if (editingId.value) {
      await axios.put(`/dashboard/members/${editingId.value}`, payload);
    } else {
      await axios.post('/dashboard/members', payload);
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to save member.';
  } finally {
    loading.value = false;
  }
}

async function saveAndClose() {
  await submit();
  if (!error.value) {
    router.visit('/dashboard/members');
  }
}

function cancel() {
  router.visit('/dashboard/members');
}

onMounted(() => {
  fetchGroups();
  if (props.routeParams.id && !isNaN(props.routeParams.id)) {
    editingId.value = props.routeParams.id;
    fetchMember();
  } else if (props.routeParams.id) {
    error.value = 'Invalid member ID. Loading create form instead.';
  }
});
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.375rem;
  padding: 0.5rem 0.75rem;
}
.input:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.3);
  border-color: #8b5cf6;
}
</style>