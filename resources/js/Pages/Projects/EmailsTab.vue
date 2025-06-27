<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Email Templates <span class="text-gray-500 text-lg font-normal">({{ templates.length }} entries)</span></h2>
      <button @click="openCreate" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center transition-all duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add Email Template
      </button>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-100">
        <thead>
          <tr class="bg-gradient-to-r from-purple-50 to-blue-50">
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:text-purple-600 transition-colors duration-200" @click="sort('name')">
              <div class="flex items-center">
                <span>Name</span>
                <svg v-if="sortKey === 'name'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                </svg>
              </div>
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:text-purple-600 transition-colors duration-200" @click="sort('type')">
              <div class="flex items-center">
                <span>Type</span>
                <svg v-if="sortKey === 'type'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                </svg>
              </div>
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:text-purple-600 transition-colors duration-200" @click="sort('subject')">
              <div class="flex items-center">
                <span>Subject</span>
                <svg v-if="sortKey === 'subject'" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                </svg>
              </div>
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="template in paginatedTemplates" :key="template.id" class="hover:bg-blue-50 transition-colors duration-150">
            <td class="px-6 py-4 text-sm text-gray-800">{{ template.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-800">{{ template.type }}</td>
            <td class="px-6 py-4 text-sm text-gray-800">{{ template.subject }}</td>
            <td class="px-6 py-4">
              <div class="flex space-x-3">
                <button @click="editTemplate(template)" class="text-blue-600 hover:text-blue-800 flex items-center transition-colors duration-200">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                  <span>Edit</span>
                </button>
                <button @click="deleteTemplate(template.id)" class="text-red-600 hover:text-red-800 flex items-center transition-colors duration-200">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  <span>Delete</span>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="paginatedTemplates.length === 0">
            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">No email templates found</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-between items-center mb-4">
      <div class="text-sm text-gray-600">Page {{ currentPage }} of {{ totalPages }} ({{ templates.length }} total)</div>
      <div class="flex space-x-2">
        <button 
          @click="prevPage" 
          :disabled="currentPage === 1" 
          class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 shadow-sm disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <span class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Previous
          </span>
        </button>
        <button 
          @click="nextPage" 
          :disabled="currentPage === totalPages" 
          class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 shadow-sm disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <span class="flex items-center">
            Next
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </button>
      </div>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto border border-gray-100">
        <div class="flex justify-between items-center mb-5">
          <h2 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">
            {{ editing ? 'Edit' : 'Create' }} Email Template
          </h2>
          <button 
            @click="closeModal" 
            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
            type="button"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveTemplate" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <div class="relative">
              <select 
                v-model="form.type" 
                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 appearance-none pr-10" 
                required
              >
                <option value="" disabled>Select type</option>
                <option value="participant_invitation">Participant invitation</option>
                <option value="participant_differential_calculation">Participant differential calculation</option>
                <option value="supporter_invitation">Supporter invitation</option>
                <option value="supporter_bill">Supporter bill</option>
                <option value="supporter_payment_reminder">Supporter payment reminder</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input 
              v-model="form.name"
              type="text"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              required
              placeholder="e.g., Participant Welcome"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input 
              v-model="form.subject"
              type="text"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              required
              placeholder="e.g., Welcome to our campaign"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email message</label>
            <textarea 
              v-model="form.body"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              rows="5"
              required
              placeholder="Enter email body..."
            ></textarea>
            <div class="mt-3 space-y-2">
              <div class="flex items-center">
                <input 
                  v-model="form.show_logo"
                  type="checkbox"
                  id="show_logo"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <label for="show_logo" class="ml-2 text-sm text-gray-700">Show logo</label>
              </div>
              <div class="flex items-center">
                <input 
                  v-model="form.show_header_image"
                  type="checkbox"
                  id="show_header_image"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <label for="show_header_image" class="ml-2 text-sm text-gray-700">Show project header image</label>
              </div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Footer</label>
            <textarea 
              v-model="form.footer"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              rows="3"
              placeholder="Enter footer text..."
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Notes</label>
            <textarea 
              v-model="form.notes"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              rows="3"
              placeholder="Enter notes..."
            ></textarea>
            <div class="mt-3">
              <div class="flex items-center">
                <input 
                  v-model="form.show_placeholders"
                  type="checkbox"
                  id="show_placeholders"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <label for="show_placeholders" class="ml-2 text-sm text-gray-700">Show available placeholders</label>
              </div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Regarding</label>
            <input 
              v-model="form.regarding"
              type="text"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              placeholder="e.g., Summer Campaign"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Reply To</label>
            <input 
              v-model="form.reply_to"
              type="email"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              placeholder="e.g., contact@sponsoring24.com"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sender Name</label>
            <input 
              v-model="form.sender_name"
              type="text"
              class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              placeholder="e.g., Sponsoring24 Team"
            />
          </div>
          <div class="flex gap-3 justify-end pt-2">
            <button 
              type="button" 
              @click="closeModal" 
              class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 shadow-sm"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg text-white hover:from-purple-700 hover:to-blue-700 transition-colors duration-200 shadow-sm" 
              :disabled="loading"
            >
              <span v-if="!loading">{{ editing ? 'Update' : 'Create' }}</span>
              <span v-else class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving...
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
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const props = defineProps({ projectId: String })
const templates = ref([])
const showModal = ref(false)
const editing = ref(false)
const form = ref({
  id: null,
  type: '',
  name: '',
  subject: '',
  body: '',
  footer: '',
  notes: '',
  show_logo: false,
  show_header_image: false,
  show_placeholders: false,
  regarding: '',
  reply_to: '',
  sender_name: ''
})
const loading = ref(false)
const error = ref('')
const currentPage = ref(1)
const itemsPerPage = 5
const sortKey = ref('name')
const sortOrder = ref('asc')

async function fetchTemplates() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await axios.get('/dashboard/email-templates', { params: { project_id: props.projectId } })
    if (data.error) {
      error.value = data.error
      templates.value = []
    } else {
      templates.value = data.data || data
    }
  } catch (e) {
    error.value = 'Failed to load email templates: ' + (e.response?.data?.error || e.message)
    templates.value = []
  } finally {
    loading.value = false
  }
}

function openCreate() {
  editing.value = false
  form.value = {
    id: null,
    type: '',
    name: '',
    subject: '',
    body: '',
    footer: '',
    notes: '',
    show_logo: false,
    show_header_image: false,
    show_placeholders: false,
    regarding: '',
    reply_to: '',
    sender_name: ''
  }
  showModal.value = true
}

function editTemplate(template) {
  editing.value = true
  form.value = { ...template }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

async function saveTemplate() {
  loading.value = true
  error.value = ''
  try {
    const payload = { ...form.value, project_id: props.projectId }
    if (editing.value) {
      await axios.put(`/dashboard/email-templates/${form.value.id}`, payload)
    } else {
      await axios.post('/dashboard/email-templates', payload)
    }
    showModal.value = false
    fetchTemplates()
  } catch (e) {
    error.value = 'Failed to save email template: ' + (e.response?.data?.error || e.message)
  } finally {
    loading.value = false
  }
}

async function deleteTemplate(id) {
  loading.value = true
  error.value = ''
  try {
    await axios.delete(`/dashboard/email-templates/${id}`)
    fetchTemplates()
  } catch (e) {
    error.value = 'Failed to delete email template: ' + (e.response?.data?.error || e.message)
  } finally {
    loading.value = false
  }
}

function sort(key) {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
  templates.value.sort((a, b) => {
    let valueA = a[key]
    let valueB = b[key]
    if (sortOrder.value === 'asc') {
      return valueA.localeCompare(valueB)
    } else {
      return valueB.localeCompare(valueA)
    }
  })
}

const paginatedTemplates = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return templates.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(templates.value.length / itemsPerPage))

function prevPage() {
  if (currentPage.value > 1) currentPage.value--
}

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++
}

onMounted(fetchTemplates)
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.375rem;
  padding: 0.5rem 0.75rem;
  width: 100%;
}

.input:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.5);
}
</style>