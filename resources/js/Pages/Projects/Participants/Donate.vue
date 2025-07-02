<template>
    <div class="min-h-screen bg-gray-50">
      <!-- Header Image -->
      <div class="relative w-full h-64">
        <img :src="project.image_url" alt="Event Image" class="w-full h-full object-cover" />
        <div class="absolute top-4 right-4">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-80 text-purple-600">fundo</span>
        </div>
      </div>
  
      <!-- Main Content -->
      <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg p-8">
          <div v-if="step === 'donation'">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ project.name }}</h1>
            <div class="text-sm text-gray-500 mb-6">
              <span>{{ project.date }} {{ project.time }}</span>
              <span class="mx-2">•</span>
              <span>@ {{ project.location }}</span>
            </div>
            <p class="text-gray-600 mb-8">{{ project.description }}</p>
  
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your support for {{ participant.first_name }} {{ participant.last_name }}</h2>
            <div class="flex items-center space-x-4 mb-6">
              <span class="flex items-center justify-center w-6 h-6 rounded-full bg-purple-100 text-purple-600">•</span>
              <span class="text-gray-700">Flat-rate donation</span>
            </div>
            <form @submit.prevent="submitDonation" class="space-y-6">
              <div class="relative">
                <input v-model="form.amount"
type="number"
class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
required
:min="project.flat_rate_enabled ? project.flat_rate_min_amount || 0 : 0"
step="0.01"
placeholder="0.00" />
                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">CHF</span>
              </div>
              <div v-if="project.flat_rate_enabled && project.flat_rate_help_text" class="text-sm text-gray-600 mt-2 mb-4">
                {{ project.flat_rate_help_text }}
              </div>
              <div v-if="project.flat_rate_enabled && project.flat_rate_min_amount > 0" class="text-sm text-blue-600 mt-2 mb-4">
                Minimum donation amount: CHF {{ project.flat_rate_min_amount }}
              </div>
              <button type="submit" class="w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200 font-medium">
                CHF {{ form.amount || 0 }} support
              </button>
            </form>
          </div>
  
          <div v-if="step === 'confirmation'">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ project.name }}</h1>
            <div class="text-sm text-gray-500 mb-6">
              <span>{{ project.date }} {{ project.time }}</span>
              <span class="mx-2">•</span>
              <span>@ {{ project.location }}</span>
            </div>
            <p class="text-gray-600 mb-8">{{ project.description }}</p>
  
            <p class="text-gray-700 mb-6">Please complete the form below with your personal details.</p>
            <p class="text-gray-700 mb-6">Your flat-rate donation: CHF {{ form.amount || 100.00 }}</p>
  
            <form @submit.prevent="submitDonation" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Gender *</label>
                  <select v-model="form.gender" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="Masculine">Masculine</option>
                    <option value="Feminine">Feminine</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">First name *</label>
                  <input v-model="form.first_name"
type="text"
class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
required />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Last name *</label>
                  <input v-model="form.last_name"
type="text"
class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
required />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Company</label>
                  <input v-model="form.company" type="text" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Address *</label>
                  <input v-model="form.address"
type="text"
class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
required />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Address suffix</label>
                  <input v-model="form.address_suffix" type="text" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Postal code *</label>
                  <input v-model="form.postal_code"
type="text"
class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
required />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Location *</label>
                  <input v-model="form.location"
type="text"
class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
required />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Country *</label>
                  <select v-model="form.country" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    <option value="Switzerland">Switzerland</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">E-mail *</label>
                  <input v-model="form.email"
type="email"
class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
required />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Phone *</label>
                  <input v-model="form.phone"
type="tel"
class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
required />
                </div>
              </div>
              <div class="flex items-center">
                <input v-model="form.privacy_policy"
type="checkbox"
class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
required />
                <label class="ml-2 text-sm text-gray-700">I accept the <a href="#" class="text-purple-600 hover:underline">privacy policy</a> and the <a href="#" class="text-purple-600 hover:underline">terms and conditions of participation</a>.*</label>
              </div>
              <button type="submit" class="w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200 font-medium">
                Save
              </button>
            </form>
          </div>
  
          <div v-if="step === 'modal'" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md">
              <h2 class="text-3xl font-bold text-gray-900 mb-4">Almost finished!</h2>
              <p class="text-gray-600 mb-4">To confirm your donation, please click the link sent to you via email at {{ confirmation_email }}.</p>
              <p class="text-gray-600 mb-6">It may take some time for the email to reach your inbox. Please also check your spam folder if you don't receive the email.</p>
              <p class="text-gray-600 mb-6">Thank you very much,</p>
              <p class="text-gray-900 font-medium">{{ project.name }}</p>
              <div class="mt-6 flex justify-end">
                <button @click="closeModal" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200 font-medium">Close</button>
              </div>
            </div>
          </div>
  
          <div v-if="step === 'confirmed'" class="text-center py-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ message }}</h2>
            <p class="text-gray-600 mb-6">Your donation has been successfully processed.</p>
            <a :href="route('dashboard')" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200 font-medium">Back to Dashboard</a>
          </div>
        </div>
      </main>
  
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue'
  import { router, usePage } from '@inertiajs/vue3'
  
  const props = defineProps({
    project: Object,
    participant: Object,
    step: String,
    confirmation_email: String,
    confirmation_link: String,
    message: String,
  })
  
  const page = usePage()
  const form = ref({
    amount: 0,
    currency: 'CHF',
    gender: 'Masculine',
    first_name: '',
    last_name: '',
    company: '',
    address: '',
    address_suffix: '',
    postal_code: '',
    location: '',
    country: 'Switzerland',
    email: '',
    phone: '',
    privacy_policy: false,
  })
  
  const errors = ref({})
  const loading = ref(false)
  
  const flashMessage = computed(() => ({
    success: page.props.flash?.success || '',
    error: page.props.flash?.error || '',
  }))
  
  function submitDonation() {
    loading.value = true
    errors.value = {}
    
    // Validate minimum amount for flat-rate donations
    if (props.project.flat_rate_enabled && props.project.flat_rate_min_amount > 0 && form.value.amount < props.project.flat_rate_min_amount) {
      errors.value = { amount: [`Minimum donation amount is CHF ${props.project.flat_rate_min_amount}`] }
      loading.value = false
      return
    }
  
    router.post(`/projects/${props.project.id}/participants/${props.participant.id}/donate`, {
      ...form.value,
      step: props.step === 'donation' ? 'donation' : 'confirmation',
    }, {
      onSuccess: () => {
        loading.value = false
        console.log('Form submission successful. Current step:', props.step);
      },
      onError: (err) => {
        errors.value = err
        loading.value = false
        console.error('Form submission failed:', err);
      },
    })
  }
  
  function closeModal() {
    router.visit(route('dashboard'))
  }
  </script>
  
  <style scoped>
  .input {
    @apply border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 transition duration-150;
  }
  </style>