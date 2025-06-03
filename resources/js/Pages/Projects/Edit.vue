<template>
  <DashboardLayout>
    <div class="max-w-5xl mx-auto py-8">
      <h1 class="text-3xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">{{ form.name.de || form.name[Object.keys(form.name)[0]] || 'Edit project' }}</h1>
      <!-- Tabs -->
      <div class="flex border-b mb-8 gap-1">
        <button 
          v-for="tab in tabs"
          :key="tab"
          @click="setActiveTab(tab)"
          :class="[
            'px-5 py-3 font-medium rounded-t-lg transition-colors duration-200 flex items-center', 
            activeTab === tab 
              ? 'border-b-2 border-purple-600 bg-gradient-to-r from-purple-50 to-blue-50 text-purple-700' 
              : 'text-gray-600 hover:text-purple-600 hover:bg-gray-50'
          ]"
        >
          <svg v-if="tab === 'Settings'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <svg v-if="tab === 'Analytics'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          <svg v-if="tab === 'Emails'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <svg v-if="tab === 'Donations'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-if="tab === 'Participant'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          {{ tab }}
        </button>
      </div>
      <div v-if="loading" class="text-center text-gray-500">Loading...</div>
      <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
      <div v-else>
        <div v-if="activeTab === 'Settings'">
          <form @submit.prevent="submit" class="space-y-8">
            <!-- Project Info -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
              <h2 class="font-semibold text-xl mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Project Information
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Project name*
                  </label>
                  <input 
                    v-model="form.name.de"
                    type="text"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                    required
                    @input="updateNameTranslations" 
                    placeholder="German"
                  />
                  <input 
                    v-model="form.name.fr"
                    type="text"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200 mt-2"
                    @input="updateNameTranslations" 
                    placeholder="French"
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
                    placeholder="City, Country"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                    Language*
                  </label>
                  <select 
                    v-model="form.language" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                    required
                  >
                    <option value="de">German</option>
                    <option value="fr">French</option>
                  </select>
                </div>
                <div></div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Start*
                  </label>
                  <input 
                    v-model="form.start"
                    type="datetime-local"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                    required 
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    End*
                  </label>
                  <input 
                    v-model="form.end"
                    type="datetime-local"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                    required 
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Allow donation until*
                  </label>
                  <input 
                    v-model="form.allow_donation_until"
                    type="datetime-local"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                    required 
                  />
                </div>
              </div>
              <div class="mt-8">
                <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                  </svg>
                  Project description*
                </label>
                <div class="space-y-3">
                  <div>
                    <div class="flex items-center mb-1">
                      <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">German</span>
                    </div>
                    <textarea 
                      v-model="form.description.de"
                      rows="5"
                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                      required
                      @input="updateDescriptionTranslations"
                      placeholder="Enter project description in German"
                    ></textarea>
                  </div>
                  <div>
                    <div class="flex items-center mb-1">
                      <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">French</span>
                    </div>
                    <textarea 
                      v-model="form.description.fr"
                      rows="5"
                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                      @input="updateDescriptionTranslations"
                      placeholder="Enter project description in French"
                    ></textarea>
                  </div>
                </div>
              </div>
              <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Project image landscape format (1200x300px)*
                  </label>
                  <div class="mt-1 flex items-center">
                    <input 
                      type="file"
                      @change="onFileChange($event, 'image_landscape')"
                      class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
                      accept="image/*" 
                    />
                  </div>
                  <div v-if="form.image_landscape_url" class="mt-3">
                    <div class="relative rounded-lg overflow-hidden shadow-sm border border-gray-200 max-w-full h-48">
                      <img :src="form.image_landscape_url" alt="Landscape preview" class="w-full h-full object-cover" />
                      <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
                    </div>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Project image square (400x400px)*
                  </label>
                  <div class="mt-1 flex items-center">
                    <input 
                      type="file"
                      @change="onFileChange($event, 'image_square')"
                      class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
                      accept="image/*" 
                    />
                  </div>
                  <div v-if="form.image_square_url" class="mt-3">
                    <div class="relative rounded-lg overflow-hidden shadow-sm border border-gray-200 w-48 h-48">
                      <img :src="form.image_square_url" alt="Square preview" class="w-full h-full object-cover" />
                      <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Donation Options -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
              <h2 class="font-semibold text-xl mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Donation Options
              </h2>
              <div class="flex items-center mb-3">
                <h3 class="font-semibold text-lg flex-1 text-gray-800 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                  </svg>
                  Flat-rate donations
                </h3>
                <div class="flex items-center">
                  <input 
                    type="checkbox"
                    v-model="form.flat_rate_enabled"
                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                    id="flat_rate_enabled" 
                  />
                  <label for="flat_rate_enabled" class="ml-2 text-sm font-medium text-gray-700">Activate flat-rate donations</label>
                </div>
              </div>
              <p class="text-sm text-gray-600 mb-4 pl-7">Enable flat-rate donations. For example, a flat rate of CHF 20 for one participant.</p>
              <div v-if="form.flat_rate_enabled" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 pl-7">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Minimum amount (CHF)
                  </label>
                  <input 
                    v-model="form.flat_rate_min_amount"
type="number"
min="0"
step="0.01"
class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Help text
                  </label>
                  <input 
                    v-model="form.flat_rate_help_text" 
                    type="text" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                    placeholder="Optional help text for donors"
                  />
                </div>
              </div>
            </div>
            <!-- Unit-based donations -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
              <div class="flex items-center mb-3">
                <h3 class="font-semibold text-lg flex-1 text-gray-800 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                  </svg>
                  Unit-based donations
                </h3>
                <div class="flex items-center">
                  <input 
                    type="checkbox"
                    v-model="form.unit_based_enabled"
                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                    id="unit_based_enabled" 
                  />
                  <label for="unit_based_enabled" class="ml-2 text-sm font-medium text-gray-700">Allow unit-based donations</label>
                </div>
              </div>
              <p class="text-sm text-gray-600 mb-4 pl-7">Allow unit-based donations. For example, CHF 1 per lap run or per kg of waste paper collected.</p>
            </div>
            <!-- Public project donation page -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
              <div class="flex items-center mb-3">
                <h3 class="font-semibold text-lg flex-1 text-gray-800 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Public project donation page, banner and poster
                </h3>
                <div class="flex items-center">
                  <input 
                    type="checkbox"
                    v-model="form.public_donation_enabled"
                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                    id="public_donation_enabled" 
                  />
                  <label for="public_donation_enabled" class="ml-2 text-sm font-medium text-gray-700">Allow public project donations</label>
                </div>
              </div>
              <p class="text-sm text-gray-600 mb-4 pl-7">Receive a link to your project with an integrated donation form. Donations received through this form cannot be allocated to any member. Share this page on your channels to promote the project and receive additional donations.</p>
            </div>
            <!-- Buttons -->
            <div class="flex justify-end gap-4 mt-8">
              <button 
                type="button" 
                @click="cancel" 
                class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-200 transition-colors duration-200 flex items-center"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
              </button>
              <button 
                type="submit" 
                class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 text-white font-medium hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition-all duration-200 shadow-sm flex items-center" 
                :disabled="loading"
                :class="{'opacity-70 cursor-not-allowed': loading}"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ loading ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>
        <div v-else-if="activeTab === 'Analytics'">
          <AnalyticsTab v-if="activeTab === 'Analytics'" :projectId="projectId" />
        </div>
        <div v-else-if="activeTab === 'Emails'">
          <EmailsTab v-if="activeTab === 'Emails'" :projectId="projectId" />
        </div>
        <div v-else-if="activeTab === 'Donations'">
          <DonationsTab v-if="activeTab === 'Donations'" :project="project" :filters="{}" />
        </div>
        <div v-else-if="activeTab === 'Participant'">
          <ParticipantsTab :projectId="projectId" />
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AnalyticsTab from './AnalyticsTab.vue';
import EmailsTab from './EmailsTab.vue';
import DonationsTab from './DonationsTab.vue';
import ProjectImageUpload from './ProjectImageUpload.vue';
import ParticipantsTab from './ParticipantsTab.vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { route } from 'ziggy-js';

const tabs = ['Settings', 'Analytics', 'Emails', 'Donations', 'Participant'];
const activeTab = ref('Settings');
const projectId = ref(window.location.pathname.split('/').slice(-2, -1)[0]);
const loading = ref(false);
const error = ref('');
const form = ref({
  name: { de: '', fr: '' },
  description: { de: '', fr: '' },
  location: '',
  language: '',
  start: '',
  end: '',
  allow_donation_until: '',
  image_landscape: null,
  image_square: null,
  image_landscape_url: '',
  image_square_url: '',
  flat_rate_enabled: false,
  flat_rate_min_amount: '',
  flat_rate_help_text: '',
  unit_based_enabled: false,
  public_donation_enabled: false,
});

const page = usePage();
const props = page.props;
const project = computed(() => props.project || {});

function setActiveTab(tab) {
  activeTab.value = tab;
  const url = new URL(window.location);
  url.searchParams.set('tab', tab);
  window.history.pushState({}, '', url);
}

function onFileChange(event, field) {
  const file = event.target.files[0];
  if (file) {
    if (field === 'image_landscape') {
      form.value.image_landscape = file;
    } else if (field === 'image_square') {
      form.value.image_square = file;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      if (field === 'image_landscape') {
        form.value.image_landscape_url = e.target.result;
      } else if (field === 'image_square') {
        form.value.image_square_url = e.target.result;
      }
    };
    reader.readAsDataURL(file);
  }
}

function updateNameTranslations() {
  form.value.name = { de: form.value.name.de || '', fr: form.value.name.fr || '' };
}

function updateDescriptionTranslations() {
  form.value.description = { de: form.value.description.de || '', fr: form.value.description.fr || '' };
}

function initializeForm() {
  if (props.project) {
    form.value.name = props.project.name || { de: '', fr: '' };
    form.value.description = props.project.description || { de: '', fr: '' };
    form.value.location = props.project.location || '';
    form.value.language = props.project.language || '';
    form.value.start = props.project.start ? new Date(props.project.start).toISOString().slice(0, 16) : '';
    form.value.end = props.project.end ? new Date(props.project.end).toISOString().slice(0, 16) : '';
    form.value.allow_donation_until = props.project.allow_donation_until ? new Date(props.project.allow_donation_until).toISOString().slice(0, 16) : '';
    form.value.flat_rate_enabled = !!props.project.flat_rate_enabled;
    form.value.flat_rate_min_amount = props.project.flat_rate_min_amount || '';
    form.value.flat_rate_help_text = props.project.flat_rate_help_text || '';
    form.value.unit_based_enabled = !!props.project.unit_based_enabled;
    form.value.public_donation_enabled = !!props.project.public_donation_enabled;
    form.value.image_landscape_url = props.project.image_landscape || '';
    form.value.image_square_url = props.project.image_square || '';
  } else {
    error.value = 'Project not found.';
  }
  loading.value = false;
}

async function submit() {
  loading.value = true;
  error.value = '';
  try {
    const formData = new FormData();
    formData.append('name', JSON.stringify(form.value.name));
    formData.append('description', JSON.stringify(form.value.description));
    formData.append('location', form.value.location);
    formData.append('language', form.value.language);
    formData.append('start', form.value.start);
    formData.append('end', form.value.end);
    formData.append('allow_donation_until', form.value.allow_donation_until);
    formData.append('flat_rate_enabled', form.value.flat_rate_enabled ? '1' : '0');
    formData.append('flat_rate_min_amount', form.value.flat_rate_min_amount || '');
    formData.append('flat_rate_help_text', form.value.flat_rate_help_text || '');
    formData.append('unit_based_enabled', form.value.unit_based_enabled ? '1' : '0');
    formData.append('public_donation_enabled', form.value.public_donation_enabled ? '1' : '0');
    if (form.value.image_landscape) {
      formData.append('image_landscape', form.value.image_landscape);
    }
    if (form.value.image_square) {
      formData.append('image_square', form.value.image_square);
    }

    const updateUrl = route('projects.update', { project: projectId.value });

    await axios.post(updateUrl, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      method: 'PUT',
    });

    alert('Project updated!');
  } catch (e) {
    error.value = `Failed to update project: ${e.message}`;
    console.error('Error updating project:', e);
  } finally {
    loading.value = false;
  }
}

function cancel() {
  window.history.back();
}

onMounted(() => {
  initializeForm();
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab');
  if (tab && tabs.includes(tab)) {
    setActiveTab(tab);
  }
});

watch(() => props.project, (newProject) => {
  if (newProject) {
    initializeForm();
  }
});
</script>

<style scoped>
.input {
  @apply border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 w-full;
}
</style>