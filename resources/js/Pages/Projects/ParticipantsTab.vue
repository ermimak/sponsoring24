<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
      <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
        Participants 
        <span class="ml-2 text-gray-700 text-lg font-normal">({{ participants.length }} total)</span>
      </h2>
      <div class="flex flex-wrap gap-3">
        <button 
          @click="openMassEmailModal" 
          class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center shadow-sm"
          title="Mass Email"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          Mass Email
        </button>
        <button 
          @click="exportParticipants" 
          class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center shadow-sm"
          title="Export"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Export
        </button>
        <button 
          @click="navigateToAddPage" 
          class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg text-white hover:from-purple-700 hover:to-blue-700 transition-colors duration-200 flex items-center shadow-sm"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Participant
        </button>
      </div>
    </div>
    <p class="text-gray-600 mb-6 max-w-3xl">
      Members must be participants in a campaign to be able to collect donations. They can be added to the campaign individually or in groups.
    </p>
    <div class="flex flex-col md:flex-row gap-6 mb-6">
      <div class="bg-white shadow-sm rounded-xl p-5 w-full md:w-1/3 border border-gray-100">
        <div class="flex items-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
          <h3 class="font-semibold text-gray-800 text-lg">Filter Participants</h3>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Search by name or email</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input 
              v-model="search"
              type="text"
              class="w-full pl-10 p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              placeholder="Search participants" 
            />
          </div>
        </div>
        <div class="flex items-center mb-2">
          <input 
            type="checkbox" 
            v-model="allGroups" 
            id="allGroups"
            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
          />
          <label for="allGroups" class="ml-2 text-sm text-gray-700">Show all groups</label>
        </div>
      </div>
      <div class="flex-1 overflow-x-auto bg-white shadow-sm rounded-xl border border-gray-100">
        <table class="min-w-full divide-y divide-gray-100">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Participant</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supporters</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Revenue</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Emails</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="participant in filteredParticipants" :key="participant.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="px-6 py-5 whitespace-nowrap">
                <Link :href="`/dashboard/members/${participant.id}/edit`" class="text-blue-600 font-medium hover:text-blue-800 transition-colors duration-200">
                  {{ participant.first_name }} {{ participant.last_name }}
                </Link>
                <p class="text-sm text-gray-500 mt-1">{{ participant.email }}</p>
                <span v-if="participant.landing_page_opened" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">Landing page opened</span>
              </td>
              <td class="px-6 py-5 whitespace-nowrap font-medium">{{ participant.supporters }}</td>
              <td class="px-6 py-5 whitespace-nowrap font-medium">€ {{ participant.sales_volume }}</td>
              <td class="px-6 py-5 whitespace-nowrap">{{ participant.emails }}</td>
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="flex gap-3">
                  <button 
                    @click="viewLandingPage(participant)" 
                    class="text-blue-600 hover:text-blue-800 transition-colors duration-200 p-1.5 rounded-md hover:bg-blue-50" 
                    title="View landing page"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button 
                    @click="viewDonationPage(participant)" 
                    class="text-purple-600 hover:text-purple-800 transition-colors duration-200 p-1.5 rounded-md hover:bg-purple-50" 
                    title="View donation page"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v2h6v-2c0-1.657-1.343-3-3-3zm0-4a7 7 0 00-7 7v2h2v-2a5 5 0 0110 0v2h2v-2a7 7 0 00-7-7z" />
                    </svg>
                  </button>
                  <button 
                    @click="openSendEmailModal(participant)" 
                    class="text-emerald-600 hover:text-emerald-800 transition-colors duration-200 p-1.5 rounded-md hover:bg-emerald-50" 
                    title="Send email"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                  </button>
                </div>
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
    <div v-if="showMassEmailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg border border-gray-100">
        <div class="flex justify-between items-center mb-5">
          <h2 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Send Mass Email</h2>
          <button 
            @click="showMassEmailModal = false" 
            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
            type="button"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="sendMassEmail" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Email Template</label>
            <div class="relative">
              <select 
                v-model="massEmailForm.template_id" 
                @change="handleMassTemplateChange" 
                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 appearance-none pr-10" 
                required
              >
                <option value="" disabled>Select a template</option>
                <option v-for="template in emailTemplates" :key="template.id" :value="template.id">
                  {{ template.name }} ({{ template.type }})
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input 
              v-model="massEmailForm.subject"
              type="text"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              required 
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
            <textarea 
              v-model="massEmailForm.body"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              rows="5"
              required
            ></textarea>
          </div>
          <div class="flex gap-3 justify-end pt-2">
            <button 
              type="button" 
              @click="showMassEmailModal = false" 
              class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 shadow-sm"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg text-white hover:from-purple-700 hover:to-blue-700 transition-colors duration-200 shadow-sm" 
              :disabled="loading"
            >
              <span v-if="!loading">Send Emails</span>
              <span v-else class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending...
              </span>
            </button>
          </div>
        </form>
        <div v-if="error" class="mt-4 p-3 bg-red-50 text-red-700 rounded-lg flex items-start">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ error }}</span>
        </div>
      </div>
    </div>

    <!-- Send Email Modal -->
    <div v-if="showEmailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg border border-gray-100">
        <div class="flex justify-between items-center mb-5">
          <h2 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">
            Email to {{ selectedParticipant?.first_name }} {{ selectedParticipant?.last_name }}
          </h2>
          <button 
            @click="showEmailModal = false" 
            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
            type="button"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="sendEmail" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Email Template</label>
            <div class="relative">
              <select 
                v-model="emailForm.template_id" 
                @change="handleTemplateChange" 
                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 appearance-none pr-10" 
                required
              >
                <option value="" disabled>Select a template</option>
                <option v-for="template in emailTemplates" :key="template.id" :value="template.id">
                  {{ template.name }} ({{ template.type }})
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input 
              v-model="emailForm.subject"
              type="text"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              required 
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
            <textarea 
              v-model="emailForm.body"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              rows="5"
              required
            ></textarea>
          </div>
          <div class="flex gap-3 justify-end pt-2">
            <button 
              type="button" 
              @click="showEmailModal = false" 
              class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 shadow-sm"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg text-white hover:from-purple-700 hover:to-blue-700 transition-colors duration-200 shadow-sm" 
              :disabled="loading"
            >
              <span v-if="!loading">Send Email</span>
              <span v-else class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending...
              </span>
            </button>
          </div>
        </form>
        <div v-if="error" class="mt-4 p-3 bg-red-50 text-red-700 rounded-lg flex items-start">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ error }}</span>
        </div>
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
  const defaultTemplate = emailTemplates.value.find(t => t.type === 'participant')
  emailForm.value = {
    template_id: defaultTemplate?.id || '',
    subject: defaultTemplate?.subject || '',
    body: defaultTemplate?.body || ''
  }
  showEmailModal.value = true
}

async function openMassEmailModal() {
  // Reset form
  massEmailForm.value = {
    template_id: '',
    subject: '',
    body: ''
  }
  
  // Always fetch templates to ensure we have the latest
  loading.value = true
  error.value = ''
  try {
    await fetchEmailTemplates()
    
    if (emailTemplates.value.length === 0) {
      error.value = 'No email templates found for this project. Please create at least one template.'
      return
    }
    
    // Set default template if available
    const defaultTemplate = emailTemplates.value.find(t => t.type === 'mass_participant') || emailTemplates.value[0]
    if (defaultTemplate) {
      massEmailForm.value = {
        template_id: defaultTemplate.id || '',
        subject: defaultTemplate.subject || '',
        body: defaultTemplate.body || ''
      }
    }
    
    showMassEmailModal.value = true
  } catch (e) {
    error.value = 'Failed to load email templates: ' + (e.response?.data?.error || e.message)
    console.error('Error loading templates:', e)
  } finally {
    loading.value = false
  }
}

function handleTemplateChange() {
  if (!emailForm.value.template_id) return
  
  try {
    const template = emailTemplates.value.find(t => t.id === emailForm.value.template_id)
    if (template) {
      emailForm.value.subject = template.subject || ''
      emailForm.value.body = template.body || ''
    }
  } catch (error) {
    console.error('Error loading template:', error)
  }
}

function handleMassTemplateChange() {
  if (!massEmailForm.value.template_id) return
  
  try {
    const template = emailTemplates.value.find(t => t.id === massEmailForm.value.template_id)
    if (template) {
      massEmailForm.value.subject = template.subject || ''
      massEmailForm.value.body = template.body || ''
    }
  } catch (error) {
    console.error('Error loading template:', error)
  }
}

async function sendEmail() {
  if (!emailForm.value.template_id) {
    error.value = 'Please select an email template.'
    return
  }
  
  loading.value = true
  error.value = ''
  try {
    const response = await axios.post(route('dashboard.participants.sendEmail', selectedParticipant.value.id), {
      template_id: emailForm.value.template_id,
      subject: emailForm.value.subject,
      body: emailForm.value.body,
      project_id: props.projectId
    })
    
    showEmailModal.value = false
    alert('Email sent successfully!')
  } catch (e) {
    error.value = 'Failed to send email: ' + (e.response?.data?.error || e.message)
    console.error('Error sending email:', e)
  } finally {
    loading.value = false
  }
}

async function sendMassEmail() {
  if (!massEmailForm.value.template_id) {
    error.value = 'Please select an email template.'
    return
  }
  
  loading.value = true
  error.value = ''
  try {
    const response = await axios.post(route('project.participants.massEmail', props.projectId), {
      template_id: massEmailForm.value.template_id,
      subject: massEmailForm.value.subject,
      body: massEmailForm.value.body
    })
    
    showMassEmailModal.value = false
    
    // Show success message with details
    if (response.data.results) {
      alert(`Mass email sent successfully! ${response.data.results.success} sent, ${response.data.results.failed} failed, ${response.data.results.skipped} skipped.`)
    } else {
      alert('Mass email sent successfully!')
    }
  } catch (e) {
    error.value = 'Failed to send mass email: ' + (e.response?.data?.error || e.message)
    console.error('Error sending mass email:', e)
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