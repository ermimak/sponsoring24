<template>
  <DashboardLayout>
    <div class="max-w-5xl mx-auto py-8">
      <h1 class="text-2xl font-bold mb-6">{{ form.name.de || 'Edit project' }}</h1>
      <!-- Tabs -->
      <div class="flex border-b mb-8">
        <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
          :class="['px-4 py-2 -mb-px font-medium', activeTab === tab ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-500']">
          {{ tab }}
        </button>
      </div>
      <div v-if="activeTab === 'Settings'">
        <form @submit.prevent="submit" class="space-y-8">
          <!-- Project Info (same as Create) -->
          <div class="bg-gray-50 rounded-lg p-6 border">
            <h2 class="font-semibold text-lg mb-4">Project info</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">Project name*</label>
                <input v-model="form.name.de" type="text" class="input w-full" required />
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
                <div class="text-xs text-gray-500 font-semibold mb-1">Start*</div>
                <input v-model="form.start" type="datetime-local" class="input w-full" required />
              </div>
              <div>
                <div class="text-xs text-gray-500 font-semibold mb-1">End*</div>
                <input v-model="form.end" type="datetime-local" class="input w-full" required />
              </div>
              <div>
                <div class="text-xs text-gray-500 font-semibold mb-1">Allow donation until*</div>
                <input v-model="form.allow_donation_until" type="datetime-local" class="input w-full" required />
              </div>
            </div>
            <div class="mt-4">
              <label class="block text-sm font-medium mb-1">Project description*</label>
              <textarea v-model="form.description.de" rows="5" class="input w-full" required></textarea>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">Project image landscape format (1200x300px)*</label>
                <input type="file" @change="onFileChange($event, 'image_landscape')" class="input w-full" />
                <div v-if="form.image_landscape_url" class="mt-2">
                  <img :src="form.image_landscape_url" alt="Landscape preview" class="rounded border max-h-32" />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Project image square (400x400px)*</label>
                <input type="file" @change="onFileChange($event, 'image_square')" class="input w-full" />
                <div v-if="form.image_square_url" class="mt-2">
                  <img :src="form.image_square_url" alt="Square preview" class="rounded border max-h-32" />
                </div>
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
            <button type="submit" class="px-6 py-2 rounded bg-purple-600 text-white font-semibold">Save</button>
          </div>
        </form>
      </div>
      <div v-else-if="activeTab === 'Analytics'">
        <AnalyticsTab v-if="activeTab==='Analytics'" :projectId="projectId" />
      </div>
      <div v-else-if="activeTab === 'Emails'">
        <EmailsTab v-if="activeTab==='Emails'" :projectId="projectId" />
      </div>
      <div v-else-if="activeTab === 'Donations'">
        <DonationsTab v-if="activeTab==='Donations'" :projectId="projectId" />
      </div>
      <div v-else-if="activeTab === 'Images'">
        <ProjectImageUpload v-if="activeTab==='Images'" :projectId="projectId" />
      </div>
      <div v-else-if="activeTab === 'Participant'">
        <ParticipantModal :show="showParticipantModal" :members="participants" :projectId="projectId" @save="handleParticipantSave" @close="() => showParticipantModal = false" />
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import AnalyticsTab from './AnalyticsTab.vue'
import EmailsTab from './EmailsTab.vue'
import DonationsTab from './DonationsTab.vue'
import ProjectImageUpload from './ProjectImageUpload.vue'
import ParticipantModal from './ParticipantModal.vue'
import axios from 'axios'

const tabs = ['Settings', 'Analytics', 'Emails', 'Donations', 'Images']
const activeTab = ref('Settings')
const showParticipantModal = ref(false)
const participants = ref([])
const projectId = ref(window.location.pathname.split('/').slice(-2, -1)[0])
const loading = ref(true)
const error = ref('')
const form = ref({
  name: { de: '', fr: '' },
  description: { de: '', fr: '' },
  location: '',
  language: '',
  start: '',
  end: '',
  allow_donation_until: '',
  image_landscape_url: '',
  image_square_url: '',
  flat_rate_enabled: false,
  flat_rate_min_amount: '',
  flat_rate_help_text: '',
  unit_based_enabled: false,
  public_donation_enabled: false,
})

function onFileChange(event, field) {
  // For preview only
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = e => {
      if (field === 'image_landscape') form.value.image_landscape_url = e.target.result
      if (field === 'image_square') form.value.image_square_url = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

async function fetchProject() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await axios.get(`/api/projects/${projectId.value}`)
    form.value.name = data.name || { de: '', fr: '' }
    form.value.description = data.description || { de: '', fr: '' }
    form.value.location = data.location || ''
    form.value.language = data.language || ''
    form.value.start = data.start ? new Date(data.start).toISOString().slice(0, 16) : ''
    form.value.end = data.end ? new Date(data.end).toISOString().slice(0, 16) : ''
    form.value.allow_donation_until = data.allow_donation_until ? new Date(data.allow_donation_until).toISOString().slice(0, 16) : ''
    form.value.flat_rate_enabled = !!data.flat_rate_enabled
    form.value.flat_rate_min_amount = data.flat_rate_min_amount || ''
    form.value.flat_rate_help_text = data.flat_rate_help_text || ''
    form.value.unit_based_enabled = !!data.unit_based_enabled
    form.value.public_donation_enabled = !!data.public_donation_enabled
    form.value.image_landscape_url = data.image_landscape || ''
    form.value.image_square_url = data.image_square || ''
  } catch (e) {
    error.value = 'Failed to load project.'
  }
  loading.value = false
}

async function fetchParticipants() {
  try {
    const { data } = await axios.get('/dashboard/members')
    participants.value = data.data || data
  } catch (e) {
    error.value = 'Failed to load participants.'
  }
}

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await axios.put(`/api/projects/${projectId.value}`, form.value)
    alert('Project updated!')
  } catch (e) {
    error.value = 'Failed to update project.'
  }
  loading.value = false
}

function cancel() {
  window.history.back()
}

function openParticipantModal() {
  showParticipantModal.value = true
}

async function handleParticipantSave(selected) {
  try {
    await Promise.all(selected.map(id => axios.post(`/dashboard/projects/${projectId.value}/add-participant`, { participant_id: id })))
    showParticipantModal.value = false
    fetchParticipants()
  } catch (e) {
    error.value = 'Failed to add participants.'
  }
}

onMounted(() => {
  fetchProject()
  fetchParticipants()
})
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 