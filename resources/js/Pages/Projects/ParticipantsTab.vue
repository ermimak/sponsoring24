<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Participants {{ participants.length }} entries</h2>
      <div class="flex space-x-2">
        <button @click="openMassEmailModal" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded flex items-center" title="Mass Email">
          <svg class="w-4 h-4 mr-1"
fill="none"
stroke="currentColor"
viewBox="0 0 24 24">
            <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M3 8l9-6 9 6v12a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
            <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M3 8l9 6 9-6"/>
          </svg>
          Mass email
        </button>
        <button @click="exportParticipants" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded flex items-center" title="Export">
          <svg class="w-4 h-4 mr-1"
fill="none"
stroke="currentColor"
viewBox="0 0 24 24">
            <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Export
        </button>
        <button @click="navigateToAddPage" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
          Add
        </button>
      </div>
    </div>
    <p class="text-gray-600 mb-4">
      Members must be participants in a project to be able to collect donations. They can be added to the project individually or in groups.
    </p>
    <div class="flex flex-col md:flex-row gap-4 mb-4">
      <div class="bg-gray-50 border rounded p-4 w-full md:w-1/3">
        <h3 class="font-semibold mb-2">Filter</h3>
        <label class="block text-sm font-medium mb-1">Search</label>
        <input v-model="search"
type="text"
class="input w-full mb-2"
placeholder="Search" />
        <label class="flex items-center">
          <input type="checkbox" v-model="allGroups" class="mr-2" /> All groups
        </label>
      </div>
      <div class="flex-1 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg border">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supporters</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales volume</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emails</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="participant in filteredParticipants" :key="participant.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <Link :href="`/dashboard/members/${participant.id}/edit`" class="text-indigo-600 hover:text-indigo-900">
                  {{ participant.first_name }} {{ participant.last_name }}
                </Link>
                <p class="text-sm text-gray-500">{{ participant.email }}</p>
                <span v-if="participant.landing_page_opened" class="text-green-600 text-xs">Landing page opened</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">{{ participant.supporters }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ participant.sales_volume }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ participant.emails }}</td>
              <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                <button @click="viewLandingPage(participant)" class="text-purple-600 hover:text-purple-800 flex items-center" title="To the participant landing page">
                  <svg class="w-4 h-4 mr-1"
fill="none"
stroke="currentColor"
viewBox="0 0 24 24">
                    <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
                <button @click="viewDonationPage(participant)" class="text-purple-600 hover:text-purple-800 flex items-center" title="Public Donation Page">
                  <svg class="w-4 h-4 mr-1"
fill="none"
stroke="currentColor"
viewBox="0 0 24 24">
                    <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M12 8c-1.657 0-3 1.343-3 3v2h6v-2c0-1.657-1.343-3-3-3zm0-4a7 7 0 00-7 7v2h2v-2a5 5 0 0110 0v2h2v-2a7 7 0 00-7-7z"/>
                  </svg>
                </button>
                <button @click="openSendEmailModal(participant)" class="text-purple-600 hover:text-purple-800 flex items-center" title="Send Email">
                  <svg class="w-4 h-4 mr-1"
fill="none"
stroke="currentColor"
viewBox="0 0 24 24">
                    <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M3 8l9-6 9 6v12a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                    <path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M3 8l9 6 9-6"/>
                  </svg>
                </button>
              </td>
            </tr>
            <tr v-if="filteredParticipants.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-gray-400">No entries found 🥺</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mass Email Modal -->
    <div v-if="showMassEmailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">Send Mass Email</h2>
        <form @submit.prevent="sendMassEmail">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Select Email Template</label>
            <select v-model="massEmailForm.template_id" class="input w-full" required>
              <option value="" disabled>Select a template</option>
              <option v-for="template in emailTemplates" :key="template.id" :value="template.id">
                {{ template.name }} ({{ template.type }})
              </option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Subject</label>
            <input v-model="massEmailForm.subject"
type="text"
class="input w-full"
required />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Message</label>
            <textarea v-model="massEmailForm.body"
class="input w-full"
rows="5"
required></textarea>
          </div>
          <div class="flex gap-2 justify-end">
            <button type="button" @click="showMassEmailModal = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded" :disabled="loading">Send</button>
          </div>
        </form>
        <div v-if="loading" class="text-gray-500 mt-2">Sending...</div>
        <div v-if="error" class="text-red-600 mt-2">{{ error }}</div>
      </div>
    </div>

    <!-- Send Email Modal -->
    <div v-if="showEmailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">Send Email to {{ selectedParticipant?.first_name }} {{ selectedParticipant?.last_name }}</h2>
        <form @submit.prevent="sendEmail">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Select Email Template</label>
            <select v-model="emailForm.template_id" class="input w-full" required>
              <option value="" disabled>Select a template</option>
              <option v-for="template in emailTemplates" :key="template.id" :value="template.id">
                {{ template.name }} ({{ template.type }})
              </option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Subject</label>
            <input v-model="emailForm.subject"
type="text"
class="input w-full"
required />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Message</label>
            <textarea v-model="emailForm.body"
class="input w-full"
rows="5"
required></textarea>
          </div>
          <div class="flex gap-2 justify-end">
            <button type="button" @click="showEmailModal = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded" :disabled="loading">Send</button>
          </div>
        </form>
        <div v-if="loading" class="text-gray-500 mt-2">Sending...</div>
        <div v-if="error" class="text-red-600 mt-2">{{ error }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3' // Import Link from Inertia
import axios from 'axios'

const page = usePage()
const props = defineProps({ projectId: String })

const participants = ref([])
const search = ref('')
const allGroups = ref(false)
const emailTemplates = ref([])
const showEmailModal = ref(false)
const showMassEmailModal = ref(false)
const selectedParticipant = ref(null)
const emailForm = ref({ template_id: '', subject: '', body: '' })
const massEmailForm = ref({ template_id: '', subject: '', body: '' })
const loading = ref(false)
const error = ref('')

async function fetchParticipants() {
  loading.value = true
  error.value = ''
  try {
    console.log('Fetching participants for project:', props.projectId)
    const { data } = await axios.get(`/dashboard/projects/${props.projectId}/participants`)
    participants.value = data.data || []
    console.log('Participants fetched:', participants.value)
  } catch (e) {
    console.error('Fetch error:', e.response ? e.response.data : e)
    error.value = 'Failed to load participants: ' + (e.response?.data?.error || e.message)
  } finally {
    loading.value = false
  }
}

async function fetchEmailTemplates() {
  try {
    const { data } = await axios.get('/dashboard/email-templates', { params: { project_id: props.projectId } })
    emailTemplates.value = data.data
  } catch (e) {
    error.value = 'Failed to load email templates: ' + (e.response?.data?.error || e.message)
  }
}

function navigateToAddPage() {
    console.log('Navigating to:', `/dashboard/projects/${props.projectId}/participants/create`)
    if (props.projectId) {
        router.visit(`/dashboard/projects/${props.projectId}/participants/create`, {
            onError: (error) => {
                console.error('Navigation failed:', error)
                alert('Failed to navigate to add page. Check console for details.')
            },
            onSuccess: () => {
                console.log('Navigation successful')
            }
        })
    } else {
        console.error('projectId is undefined')
        alert('Project ID is missing. Please reload the page.')
    }
}

function viewLandingPage(participant) {
  router.visit(`/projects/${props.projectId}/participants/${participant.id}`)
}

function viewDonationPage(participant) {
  router.visit(`/projects/${props.projectId}/participants/${participant.id}/donate`)
}

function openSendEmailModal(participant) {
  selectedParticipant.value = participant
  const defaultTemplate = emailTemplates.value.find(t => t.type === 'participant_invitation') || emailTemplates.value[0]
  emailForm.value = {
    template_id: defaultTemplate?.id || '',
    subject: defaultTemplate?.subject || '',
    body: defaultTemplate?.body || ''
  }
  showEmailModal.value = true
}

function openMassEmailModal() {
  const defaultTemplate = emailTemplates.value.find(t => t.type === 'mass_participant') || emailTemplates.value[0]
  massEmailForm.value = {
    template_id: defaultTemplate?.id || '',
    subject: defaultTemplate?.subject || '',
    body: defaultTemplate?.body || ''
  }
  showMassEmailModal.value = true
}

async function sendEmail() {
  loading.value = true
  error.value = ''
  try {
    await axios.post(`/dashboard/participants/${selectedParticipant.value.id}/send-email`, {
      ...emailForm.value,
      project_id: props.projectId
    })
    showEmailModal.value = false
  } catch (e) {
    error.value = 'Failed to send email: ' + (e.response?.data?.error || e.message)
  } finally {
    loading.value = false
  }
}

async function sendMassEmail() {
  loading.value = true
  error.value = ''
  try {
    await axios.post(`/dashboard/projects/${props.projectId}/send-mass-email`, {
      ...massEmailForm.value
    })
    showMassEmailModal.value = false
  } catch (e) {
    error.value = 'Failed to send mass email: ' + (e.response?.data?.error || e.message)
  } finally {
    loading.value = false
  }
}

async function exportParticipants() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await axios.get(`/dashboard/projects/${props.projectId}/participants/export`, {
      responseType: 'blob'
    })
    const url = window.URL.createObjectURL(new Blob([data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `participants_${props.projectId}.csv`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  } catch (e) {
    error.value = 'Failed to export participants: ' + (e.response?.data?.error || e.message)
  } finally {
    loading.value = false
  }
}

const filteredParticipants = computed(() => {
  let list = participants.value
  if (search.value) {
    const s = search.value.toLowerCase()
    list = list.filter(p =>
      `${p.first_name} ${p.last_name}`.toLowerCase().includes(s) ||
      p.email.toLowerCase().includes(s)
    )
  }
  if (!allGroups.value) {
    list = list.filter(p => p.member_groups.length > 0)
  }
  return list
})

onMounted(() => {
  fetchParticipants()
  fetchEmailTemplates()
})

watch(() => props.projectId, () => {
  fetchParticipants()
})
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style>