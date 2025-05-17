<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Profile Section -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
          <span>1 Profile</span>
          <button @click="toggleSection('profile')" class="ml-auto text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.profile" @submit.prevent="saveSettings" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Name of the organization*</label>
              <input v-model="form.organization_name" type="text" class="input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Contact person Title*</label>
              <select v-model="form.contact_title" class="input w-full" required>
                <option value="Mister">Mister</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Contact person first name*</label>
              <input v-model="form.contact_first_name" type="text" class="input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Contact person last name*</label>
              <input v-model="form.contact_last_name" type="text" class="input w-full" required />
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Address*</label>
              <input v-model="form.address" type="text" class="input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Address suffix</label>
              <input v-model="form.address_suffix" type="text" class="input w-full" />
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Postal code*</label>
              <input v-model="form.postal_code" type="text" class="input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Location*</label>
              <input v-model="form.location" type="text" class="input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">country*</label>
              <input v-model="form.country" type="text" class="input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Language*</label>
              <select v-model="form.language" class="input w-full" required>
                <option value="German">German</option>
                <option value="English">English</option>
                <option value="French">French</option>
                <option value="Italian">Italian</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">E-mail*</label>
              <input v-model="form.email" type="email" class="input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Phone*</label>
              <input v-model="form.phone" type="text" class="input w-full" required />
            </div>
          </div>
          <div v-if="form.user">
            <label class="block text-sm font-medium text-gray-700">Managed by</label>
            <p class="text-sm">{{ form.user.name }} ({{ form.user.email }})</p>
          </div>
        </form>
      </div>

      <!-- Appearance Section -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
          <span>2 Appearance</span>
          <button @click="toggleSection('appearance')" class="ml-auto text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.appearance" @submit.prevent="saveSettings" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Accent color*</label>
              <input v-model="form.accent_color" type="color" class="input w-full h-10" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Logo*</label>
              <input type="file" @change="handleLogoUpload" class="input w-full" accept="image/*" />
              <div v-if="form.logo_path" class="mt-2">
                <img :src="'/storage/' + form.logo_path" alt="Logo Preview" class="max-w-xs max-h-48" />
              </div>
              <div v-else class="mt-2 text-gray-500">Logo not chosen</div>
            </div>
          </div>
          <div class="mt-4">
            <h3 class="text-md font-medium text-gray-700">preview</h3>
            <div class="flex flex-wrap gap-2 mt-2">
              <span :style="{ color: form.accent_color, border: '1px solid ' + form.accent_color, padding: '4px 12px', borderRadius: '8px' }">Link example</span>
              <button :style="{ backgroundColor: form.accent_color }" class="px-4 py-2 rounded text-white">Button example</button>
              <button :style="{ backgroundColor: form.accent_color }" class="px-3 py-1 rounded text-white text-sm">Small button example</button>
              <button :style="{ color: form.accent_color, border: '1px solid ' + form.accent_color, padding: '4px 12px', borderRadius: '8px' }">Button example</button>
              <button :style="{ color: form.accent_color, border: '1px solid ' + form.accent_color, padding: '4px 12px', borderRadius: '8px' }">Small button example</button>
            </div>
          </div>
        </form>
      </div>

      <!-- Billing Address Section -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
          <span>3 Billing address</span>
          <button @click="toggleSection('billing')" class="ml-auto text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.billing" @submit.prevent="saveSettings" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Salutation</label>
              <select v-model="form.billing_salutation" class="input w-full">
                <option value="Mister">Mister</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">First name</label>
              <input v-model="form.billing_first_name" type="text" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Last name</label>
              <input v-model="form.billing_last_name" type="text" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Address suffix</label>
              <input v-model="form.billing_address_suffix" type="text" class="input w-full" />
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Address</label>
              <input v-model="form.billing_address" type="text" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Postal code</label>
              <input v-model="form.billing_postal_code" type="text" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Location</label>
              <input v-model="form.billing_location" type="text" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">country</label>
              <input v-model="form.billing_country" type="text" class="input w-full" />
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">E-mail</label>
              <input v-model="form.billing_email" type="email" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Phone</label>
              <input v-model="form.billing_phone" type="text" class="input w-full" />
            </div>
          </div>
        </form>
      </div>

      <!-- Bank Details Section -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
          <span>4 Bank details</span>
          <button @click="toggleSection('bank')" class="ml-auto text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.bank" @submit.prevent="saveSettings" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Bank account for transferring donations</label>
            <textarea v-model="form.bank_account_details" class="input w-full" rows="3"></textarea>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">IBAN account number</label>
              <input v-model="form.iban" type="text" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Recipient</label>
              <input v-model="form.recipient" type="text" class="input w-full" />
            </div>
          </div>
        </form>
      </div>

      <!-- Reset Password Section -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
          <span>5 Reset password</span>
          <button @click="toggleSection('password')" class="ml-auto text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.password" @submit.prevent="saveSettings" class="space-y-4">
          <p class="text-sm text-gray-600 mb-2">The password can be changed here. Leave these fields blank if you do not want to change the password. The password must be at least 8 characters long, contain one uppercase and one lowercase letter, and one number.</p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Password</label>
              <input v-model="form.password" type="password" class="input w-full" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Repeat password</label>
              <input v-model="form.password_confirmation" type="password" class="input w-full" />
            </div>
          </div>
        </form>
      </div>

      <!-- Project Overview Section -->
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
          <span>6 Project overview</span>
          <button @click="toggleSection('overview')" class="ml-auto text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </h2>
        <form v-if="activeSections.overview" @submit.prevent="saveSettings" class="space-y-4">
          <div class="flex items-center">
            <input v-model="form.project_overview_enabled" type="checkbox" id="project_overview" class="mr-2">
            <label for="project_overview" class="text-sm">Activate project overview</label>
          </div>
        </form>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end mt-6">
        <button type="submit" @click.prevent="saveSettings" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
          Save
        </button>
      </div>

      <!-- Flash Messages -->
      <div v-if="flashMessage.success" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
        {{ flashMessage.success }}
      </div>
      <div v-if="flashMessage.error" class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg">
        {{ flashMessage.error }}
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  settings: Object,
})

const page = usePage()
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
  overview: true,
})

const flashMessage = computed(() => ({
  success: page.props.flash?.success || '',
  error: page.props.flash?.error || '',
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

async function saveSettings() {
  const formData = new FormData()
  for (const key in form.value) {
    if (key === 'logo' && form.value[key]) {
      formData.append('logo', form.value[key])
    } else if (key !== 'user') {
      formData.append(key, form.value[key])
    }
  }

  try {
    const response = await axios.post('/dashboard/settings', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    if (response.data.success) {
      page.props.flash = { success: 'Settings saved successfully' }
    }
  } catch (error) {
    console.error('Failed to save settings:', error)
    page.props.flash = { error: 'Failed to save settings. Please try again.' }
  }
}

watch(() => props.settings, (newSettings) => {
  if (newSettings) {
    for (const key in newSettings) {
      if (form.value.hasOwnProperty(key)) {
        form.value[key] = newSettings[key] || form.value[key]
      }
    }
  }
}, { deep: true })
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 w-full;
}
</style>