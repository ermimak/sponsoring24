<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Emails <span class="text-gray-500">({{ templates.length }} entries)</span></h2>
      <button @click="openCreate" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add Email Template
      </button>
    </div>
    <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg border mb-4">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sort('name')">Name <span v-if="sortKey === 'name'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span></th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sort('type')">Type <span v-if="sortKey === 'type'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span></th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sort('subject')">Subject <span v-if="sortKey === 'subject'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span></th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="template in paginatedTemplates" :key="template.id" class="hover:bg-gray-50">
          <td class="px-6 py-4">{{ template.name }}</td>
          <td class="px-6 py-4">{{ template.type }}</td>
          <td class="px-6 py-4">{{ template.subject }}</td>
          <td class="px-6 py-4 flex space-x-2">
            <button @click="editTemplate(template)" class="text-indigo-600 hover:text-indigo-900 flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Edit
            </button>
            <button @click="deleteTemplate(template.id)" class="text-red-600 hover:text-red-900 flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              Delete
            </button>
          </td>
        </tr>
        <tr v-if="paginatedTemplates.length === 0">
          <td colspan="4" class="px-6 py-4 text-center text-gray-400">No email templates found</td>
        </tr>
      </tbody>
    </table>
    <div class="flex justify-between items-center">
      <div class="text-sm text-gray-500">Page {{ currentPage }} of {{ totalPages }} entries total</div>
      <div class="flex space-x-2">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Prev</button>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Next</button>
      </div>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">{{ editing ? 'Edit' : 'Create' }} Email</h2>
        <form @submit.prevent="saveTemplate" class="space-y-4">
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Type</label>
            <select v-model="form.type" class="input w-full" required>
              <option value="" disabled>Select type</option>
              <option value="participant_invitation">Participant invitation</option>
              <option value="participant_differential_calculation">Participant differential calculation</option>
              <option value="supporter_invitation">Supporter invitation</option>
              <option value="supporter_bill">Supporter bill</option>
              <option value="supporter_payment_reminder">Supporter payment reminder</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Name</label>
            <input v-model="form.name" type="text" class="input w-full" required placeholder="e.g., Teilnehmer Einladung" />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Subject</label>
            <input v-model="form.subject" type="text" class="input w-full" required placeholder="e.g., Invitation to project" />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Email message</label>
            <textarea v-model="form.body" class="input w-full" rows="5" required placeholder="Enter email body..."></textarea>
            <div class="mt-2 flex items-center">
              <input v-model="form.show_logo" type="checkbox" id="show_logo" class="mr-2">
              <label for="show_logo" class="text-sm">Show logo</label>
            </div>
            <div class="mt-2 flex items-center">
              <input v-model="form.show_header_image" type="checkbox" id="show_header_image" class="mr-2">
              <label for="show_header_image" class="text-sm">Show project header image</label>
            </div>
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">E-Mail Footer</label>
            <textarea v-model="form.footer" class="input w-full" rows="3" placeholder="Enter footer text..."></textarea>
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Email notes</label>
            <textarea v-model="form.notes" class="input w-full" rows="3" placeholder="Enter notes..."></textarea>
            <div class="mt-2 flex items-center">
              <input v-model="form.show_placeholders" type="checkbox" id="show_placeholders" class="mr-2">
              <label for="show_placeholders" class="text-sm">Show available placeholders</label>
            </div>
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Regarding</label>
            <input v-model="form.regarding" type="text" class="input w-full" placeholder="e.g., Weihnachten für alle" />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Reply To</label>
            <input v-model="form.reply_to" type="email" class="input w-full" placeholder="e.g., reto.schaufelberger@cheselhaus.ch" />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Sender name</label>
            <input v-model="form.sender_name" type="text" class="input w-full" placeholder="e.g., Cheselhaus Events" />
          </div>
          <div class="flex gap-2 justify-end mt-4">
            <button type="button" @click="closeModal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded" :disabled="loading">
              {{ loading ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
        <div v-if="loading" class="text-gray-500">Loading...</div>
        <div v-if="error" class="text-red-600">{{ error }}</div>
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
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 w-full;
}
</style>