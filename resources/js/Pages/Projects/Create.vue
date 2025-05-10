<template>
  <DashboardLayout>
    <div class="max-w-5xl mx-auto py-8">
      <h1 class="text-2xl font-bold mb-6">Add project</h1>
      <form @submit.prevent="submit" class="space-y-8">
        <!-- Project Info -->
        <div class="bg-gray-50 rounded-lg p-6 border">
          <h2 class="font-semibold text-lg mb-4">Project info</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Project name*</label>
              <div class="space-y-2">
                <div>
                  <label class="text-xs text-gray-500">German</label>
                  <input v-model="form.name.de" type="text" class="input w-full" required />
                </div>
                <div>
                  <label class="text-xs text-gray-500">French</label>
                  <input v-model="form.name.fr" type="text" class="input w-full" required />
                </div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Location</label>
              <input v-model="form.location" type="text" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Language*</label>
              <select v-model="form.language" class="input w-full" required>
                <option value="de">German</option>
                <option value="fr">French</option>
              </select>
            </div>
            <div></div>
            <div>
              <label class="block text-sm font-medium mb-1">Start*</label>
              <input v-model="form.start" type="date" class="input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">End*</label>
              <input v-model="form.end" type="date" class="input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Allow donation until*</label>
              <input v-model="form.allow_donation_until" type="date" class="input w-full" required />
            </div>
          </div>
          <div class="mt-4">
            <label class="block text-sm font-medium mb-1">Project description*</label>
            <div class="space-y-2">
              <div>
                <label class="text-xs text-gray-500">German</label>
                <textarea v-model="form.description.de" rows="5" class="input w-full" required></textarea>
              </div>
              <div>
                <label class="text-xs text-gray-500">French</label>
                <textarea v-model="form.description.fr" rows="5" class="input w-full" required></textarea>
              </div>
            </div>
          </div>
          <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Project image landscape format (1200x300px)*</label>
              <input type="file" @change="onFileChange($event, 'image_landscape')" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Project image square (400x400px)*</label>
              <input type="file" @change="onFileChange($event, 'image_square')" class="input w-full" />
            </div>
          </div>
        </div>

        <!-- Flat-rate donations -->
        <div class="bg-gray-50 rounded-lg p-6 border">
          <div class="flex items-center mb-2">
            <h2 class="font-semibold text-lg flex-1">Flat-rate donations</h2>
            <input type="checkbox" v-model="form.flat_rate_enabled" class="mr-2" id="flat_rate_enabled" />
            <label for="flat_rate_enabled" class="text-sm">Activate flat-rate donations</label>
          </div>
          <p class="text-sm text-gray-600 mb-2">Enable flat-rate donations. For example, a flat rate of CHF 20 for one participant.</p>
          <div v-if="form.flat_rate_enabled" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
              <label class="block text-sm font-medium mb-1">Minimum amount (CHF)</label>
              <input v-model="form.flat_rate_min_amount" type="number" min="0" step="0.01" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Help text</label>
              <input v-model="form.flat_rate_help_text" type="text" class="input w-full" />
            </div>
          </div>
        </div>

        <!-- Unit-based donations -->
        <div class="bg-gray-50 rounded-lg p-6 border">
          <div class="flex items-center mb-2">
            <h2 class="font-semibold text-lg flex-1">Unit-based donations</h2>
            <input type="checkbox" v-model="form.unit_based_enabled" class="mr-2" id="unit_based_enabled" />
            <label for="unit_based_enabled" class="text-sm">Allow unit-based donations</label>
          </div>
          <p class="text-sm text-gray-600">Allow unit-based donations. For example, CHF 1 per lap run or per kg of waste paper collected.</p>
        </div>

        <!-- Public project donation page -->
        <div class="bg-gray-50 rounded-lg p-6 border">
          <div class="flex items-center mb-2">
            <h2 class="font-semibold text-lg flex-1">Public project donation page, banner and poster</h2>
            <input type="checkbox" v-model="form.public_donation_enabled" class="mr-2" id="public_donation_enabled" />
            <label for="public_donation_enabled" class="text-sm">Allow public project donations</label>
          </div>
          <p class="text-sm text-gray-600">Receive a link to your project with an integrated donation form. Donations received through this form cannot be allocated to any member. Share this page on your channels to promote the project and receive additional donations.</p>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-4">
          <button type="button" @click="cancel" class="px-6 py-2 rounded bg-gray-100 text-gray-700">Cancel</button>
          <button type="submit" :disabled="loading" class="px-6 py-2 rounded bg-purple-600 text-white font-semibold">Save</button>
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