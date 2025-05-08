<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Emails</h2>
      <button @click="openCreate" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">+ Add Email Template</button>
    </div>
    <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg border mb-4">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="template in templates" :key="template.id">
          <td class="px-6 py-4">{{ template.name }}</td>
          <td class="px-6 py-4">{{ template.type }}</td>
          <td class="px-6 py-4">{{ template.subject }}</td>
          <td class="px-6 py-4">
            <button @click="editTemplate(template)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
            <button @click="deleteTemplate(template.id)" class="text-red-600 hover:text-red-900">Delete</button>
          </td>
        </tr>
        <tr v-if="templates.length === 0">
          <td colspan="4" class="px-6 py-4 text-center text-gray-400">No email templates found</td>
        </tr>
      </tbody>
    </table>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">{{ editing ? 'Edit' : 'Create' }} Email Template</h2>
        <form @submit.prevent="saveTemplate">
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Type</label>
            <input v-model="form.type" class="input w-full" required />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Name</label>
            <input v-model="form.name" class="input w-full" required />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Subject</label>
            <input v-model="form.subject" class="input w-full" required />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Body</label>
            <textarea v-model="form.body" class="input w-full" rows="5" required></textarea>
          </div>
          <div class="flex gap-2 justify-end mt-4">
            <button type="button" @click="closeModal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded" :disabled="loading">Save</button>
          </div>
        </form>
        <div v-if="loading" class="text-gray-500">Loading...</div>
        <div v-if="error" class="text-red-600">{{ error }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({ projectId: Number })
const templates = ref([])
const showModal = ref(false)
const editing = ref(false)
const form = ref({ id: null, type: '', name: '', subject: '', body: '' })
const loading = ref(false)
const error = ref('')

async function fetchTemplates() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await axios.get('/dashboard/email-templates', { params: { project_id: props.projectId } })
    templates.value = data.data || data
  } catch (e) {
    error.value = 'Failed to load email templates.'
  }
  loading.value = false
}

function openCreate() {
  editing.value = false
  form.value = { id: null, type: '', name: '', subject: '', body: '' }
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
    if (editing.value) {
      await axios.put(`/dashboard/email-templates/${form.value.id}`, form.value)
    } else {
      await axios.post('/dashboard/email-templates', { ...form.value, project_id: props.projectId })
    }
    showModal.value = false
    fetchTemplates()
  } catch (e) {
    error.value = 'Failed to save email template.'
  }
  loading.value = false
}

async function deleteTemplate(id) {
  loading.value = true
  error.value = ''
  try {
    await axios.delete(`/dashboard/email-templates/${id}`)
    fetchTemplates()
  } catch (e) {
    error.value = 'Failed to delete email template.'
  }
  loading.value = false
}

onMounted(fetchTemplates)
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 