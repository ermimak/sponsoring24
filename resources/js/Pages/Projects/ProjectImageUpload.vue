<template>
  <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
    <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      Project Images
    </h2>
    <form @submit.prevent="uploadImages" class="space-y-6">
      <div class="mb-4 bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z" />
          </svg>
          Landscape Image <span class="text-xs text-gray-500 ml-1">(1200x300px)</span>
        </label>
        <div class="mt-1 flex items-center">
          <label class="block w-full">
            <span class="sr-only">Choose landscape image</span>
            <input 
              type="file" 
              @change="onFileChange($event, 'landscape')" 
              accept="image/*" 
              class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-lg file:border-0
                file:text-sm file:font-medium
                file:bg-purple-50 file:text-purple-700
                hover:file:bg-purple-100 hover:file:text-purple-800
                focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2
                cursor-pointer"
            />
          </label>
        </div>
        <div v-if="preview.landscape" class="mt-3">
          <div class="relative rounded-lg overflow-hidden border border-gray-200 shadow-sm">
            <img :src="preview.landscape" class="w-full max-w-lg" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
          </div>
          <p class="text-xs text-gray-500 mt-1">Preview of landscape image</p>
        </div>
      </div>
      
      <div class="mb-4 bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2H5z" />
          </svg>
          Square Image <span class="text-xs text-gray-500 ml-1">(400x400px)</span>
        </label>
        <div class="mt-1 flex items-center">
          <label class="block w-full">
            <span class="sr-only">Choose square image</span>
            <input 
              type="file" 
              @change="onFileChange($event, 'square')" 
              accept="image/*" 
              class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-lg file:border-0
                file:text-sm file:font-medium
                file:bg-purple-50 file:text-purple-700
                hover:file:bg-purple-100 hover:file:text-purple-800
                focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2
                cursor-pointer"
            />
          </label>
        </div>
        <div v-if="preview.square" class="mt-3">
          <div class="relative w-40 h-40 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
            <img :src="preview.square" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
          </div>
          <p class="text-xs text-gray-500 mt-1">Preview of square image</p>
        </div>
      </div>
      
      <div class="flex justify-end">
        <button 
          type="submit" 
          class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-md flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="loading"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
          </svg>
          {{ loading ? 'Uploading...' : 'Upload Images' }}
        </button>
      </div>
    </form>
    
    <!-- Success Message -->
    <div v-if="message" class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 rounded-lg flex items-start border border-green-100 shadow-sm">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ message }}</span>
    </div>
    
    <!-- Error Message -->
    <div v-if="error" class="mt-4 p-4 bg-gradient-to-r from-red-50 to-pink-50 text-red-700 rounded-lg flex items-start border border-red-100 shadow-sm">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ error }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({ projectId: String })
const files = ref({ landscape: null, square: null })
const preview = ref({ landscape: '', square: '' })
const message = ref('')
const loading = ref(false)
const error = ref('')

function onFileChange(e, type) {
  const file = e.target.files[0]
  files.value[type] = file
  if (file) {
    const reader = new FileReader()
    reader.onload = (ev) => { preview.value[type] = ev.target.result }
    reader.readAsDataURL(file)
  }
}

async function uploadImages() {
  loading.value = true
  error.value = ''
  try {
    const formData = new FormData()
    if (files.value.landscape) formData.append('image_landscape', files.value.landscape)
    if (files.value.square) formData.append('image_square', files.value.square)
    await axios.post(`/dashboard/projects/${props.projectId}/upload-image`, formData)
    message.value = 'Images uploaded successfully!'
  } catch (e) {
    error.value = 'Failed to upload images.'
  }
  loading.value = false
}
</script> 