<template>
  <DashboardLayout>
    <div class="max-w-5xl mx-auto py-8">
      <h1 class="text-3xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Add Project</h1>
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
              <div class="space-y-3">
                <div>
                  <div class="flex items-center mb-1">
                    <label class="text-xs text-gray-500 flex items-center">
                      <span class="inline-flex items-center justify-center rounded-full bg-purple-100 px-1.5 py-0.5 text-xs font-medium text-purple-700 mr-1.5">DE</span>
                      German
                    </label>
                  </div>
                  <input 
                    v-model="form.name.de"
                    type="text"
                    placeholder="Enter project name in German"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                    required 
                  />
                </div>
                <!-- <div>
                  <div class="flex items-center mb-1">
                    <label class="text-xs text-gray-500 flex items-center">
                      <span class="inline-flex items-center justify-center rounded-full bg-blue-100 px-1.5 py-0.5 text-xs font-medium text-blue-700 mr-1.5">FR</span>
                      French
                    </label>
                  </div>
                  <input 
                    v-model="form.name.fr" 
                    type="text" 
                    placeholder="Enter project name in French"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
                    required 
                  />
                </div> -->
              </div>
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
                placeholder="Enter project location"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
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
                <option value="">Select language</option>
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
                Start date*
              </label>
              <input 
                v-model="form.start"
                type="date"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                required 
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                End date*
              </label>
              <input 
                v-model="form.end"
                type="date"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                required 
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Allow donation until*
              </label>
              <input 
                v-model="form.allow_donation_until"
                type="date"
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
            <div class="space-y-3 mt-2">
              <div>
                <div class="flex items-center mb-1">
                  <label class="text-xs text-gray-500 flex items-center">
                    <span class="inline-flex items-center justify-center rounded-full bg-purple-100 px-1.5 py-0.5 text-xs font-medium text-purple-700 mr-1.5">DE</span>
                    German
                  </label>
                </div>
                <textarea 
                  v-model="form.description.de"
                  rows="5"
                  placeholder="Enter project description in German"
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
                  required
                ></textarea>
              </div>
              <!-- <div>
                <label class="text-xs text-gray-500">French</label>
                <textarea v-model="form.description.fr" rows="5" class="input w-full" required></textarea>
              </div> -->
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
              <div class="mt-1 relative">
                <label class="flex justify-center px-4 py-3 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 hover:border-purple-300 transition-colors duration-200">
                  <div class="space-y-1 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <div class="text-sm text-gray-600">
                      <span class="font-medium text-purple-600 hover:text-purple-500">Click to upload</span> or drag and drop
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                  </div>
                  <input type="file" @change="onFileChange($event, 'image_landscape')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                </label>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Project image square (400x400px)*
              </label>
              <div class="mt-1 relative">
                <label class="flex justify-center px-4 py-3 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 hover:border-purple-300 transition-colors duration-200">
                  <div class="space-y-1 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <div class="text-sm text-gray-600">
                      <span class="font-medium text-purple-600 hover:text-purple-500">Click to upload</span> or drag and drop
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                  </div>
                  <input type="file" @change="onFileChange($event, 'image_square')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Flat-rate donations -->
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
          <h2 class="font-semibold text-xl mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Donation Options
          </h2>
          <div class="flex items-center mb-3">
            <h3 class="font-semibold text-lg flex-1 text-gray-800 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
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
                placeholder="0.00"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              />
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
                placeholder="Donation help text"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              />
              <p class="text-xs text-gray-500 mt-1">This text will be displayed to donors as guidance</p>
            </div>
          </div>

          <div class="flex items-center mb-3 mt-8">
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
            {{ loading ? 'Creating...' : 'Create Project' }}
          </button>
        </div>
      </form>
      <div v-if="error" class="text-red-600 mb-2">{{ error }}</div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'

const form = ref({
  name: {
    de: '',
    fr: ''
  },
  location: '',
  language: 'de',
  start: '',
  end: '',
  allow_donation_until: '',
  description: {
    de: '',
    fr: ''
  },
  image_landscape: null,
  image_square: null,
  flat_rate_enabled: false,
  flat_rate_min_amount: '',
  flat_rate_help_text: '',
  unit_based_enabled: false,
  public_donation_enabled: false,
})

const error = ref('')
const loading = ref(false)

function onFileChange(event, field) {
  form.value[field] = event.target.files[0]
}

async function submit() {
  loading.value = true
  error.value = ''
  try {
    // First upload the images if they exist
    let imageLandscapePath = null
    let imageSquarePath = null

    if (form.value.image_landscape) {
      const imageFormData = new FormData()
      imageFormData.append('image_landscape', form.value.image_landscape)
      const response = await axios.post('/upload', imageFormData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      imageLandscapePath = response.data.image_landscape
    }

    if (form.value.image_square) {
      const imageFormData = new FormData()
      imageFormData.append('image_square', form.value.image_square)
      const response = await axios.post('/upload', imageFormData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      imageSquarePath = response.data.image_square
    }

    // Then create the project with the file paths
    const formData = new FormData()
    
    // Add translatable fields
    formData.append('name[de]', form.value.name.de)
    formData.append('name[fr]', form.value.name.fr)
    formData.append('description[de]', form.value.description.de)
    formData.append('description[fr]', form.value.description.fr)
    
    // Add other text fields
    formData.append('location', form.value.location)
    formData.append('language', form.value.language)
    formData.append('start', form.value.start)
    formData.append('end', form.value.end)
    formData.append('allow_donation_until', form.value.allow_donation_until)
    
    // Add image paths if they exist
    if (imageLandscapePath) {
      formData.append('image_landscape', imageLandscapePath)
    }
    if (imageSquarePath) {
      formData.append('image_square', imageSquarePath)
    }
    
    // Add boolean fields
    formData.append('flat_rate_enabled', form.value.flat_rate_enabled ? '1' : '0')
    formData.append('unit_based_enabled', form.value.unit_based_enabled ? '1' : '0')
    formData.append('public_donation_enabled', form.value.public_donation_enabled ? '1' : '0')
    
    // Add flat rate fields if enabled
    if (form.value.flat_rate_enabled) {
      formData.append('flat_rate_min_amount', form.value.flat_rate_min_amount)
      formData.append('flat_rate_help_text', form.value.flat_rate_help_text)
    }

    await axios.post('/dashboard/projects', formData)
    window.location.href = '/dashboard/projects'
  } catch (e) {
    if (e.response?.data?.errors) {
      error.value = Object.values(e.response.data.errors).flat().join(', ')
    } else {
      error.value = 'Failed to create project.'
    }
  }
  loading.value = false
}

function cancel() {
  window.location.href = '/dashboard/projects'
}
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 