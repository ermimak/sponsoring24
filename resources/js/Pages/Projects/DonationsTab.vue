<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Donations</h2>
      <button @click="openCreate" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">+ Add Donation</button>
    </div>
    <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg border mb-4">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billing Date</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="donation in donations" :key="donation.id">
          <td class="px-6 py-4">{{ donation.type }}</td>
          <td class="px-6 py-4">{{ donation.participant?.first_name }} {{ donation.participant?.last_name }}</td>
          <td class="px-6 py-4">CHF {{ donation.amount }}</td>
          <td class="px-6 py-4">{{ donation.status }}</td>
          <td class="px-6 py-4">{{ donation.billing_date }}</td>
          <td class="px-6 py-4">
            <button @click="editDonation(donation)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
            <button @click="deleteDonation(donation.id)" class="text-red-600 hover:text-red-900">Delete</button>
          </td>
        </tr>
        <tr v-if="donations.length === 0">
          <td colspan="6" class="px-6 py-4 text-center text-gray-400">No donations found</td>
        </tr>
      </tbody>
    </table>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">{{ editing ? 'Edit' : 'Create' }} Donation</h2>
        <form @submit.prevent="saveDonation">
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Type</label>
            <input v-model="form.type" class="input w-full" required />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Participant ID</label>
            <input v-model="form.participant_id" class="input w-full" />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Amount</label>
            <input v-model="form.amount" type="number" min="0" step="0.01" class="input w-full" required />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Status</label>
            <input v-model="form.status" class="input w-full" />
          </div>
          <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Billing Date</label>
            <input v-model="form.billing_date" type="date" class="input w-full" />
          </div>
          <div v-if="loading" class="text-gray-500">Loading...</div>
          <div v-if="error" class="text-red-600">{{ error }}</div>
          <div class="flex gap-2 justify-end mt-4">
            <button type="button" @click="closeModal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded" :disabled="loading">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({ projectId: Number })
donations = ref([])
const showModal = ref(false)
const editing = ref(false)
const form = ref({ id: null, type: '', participant_id: '', amount: '', status: '', billing_date: '' })
const loading = ref(false)
const error = ref('')

async function fetchDonations() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await axios.get('/dashboard/donations', { params: { project_id: props.projectId } })
    donations.value = data.data || data
  } catch (e) {
    error.value = 'Failed to load donations.'
  }
  loading.value = false
}

function openCreate() {
  editing.value = false
  form.value = { id: null, type: '', participant_id: '', amount: '', status: '', billing_date: '' }
  showModal.value = true
}

function editDonation(donation) {
  editing.value = true
  form.value = { ...donation, participant_id: donation.participant_id || '' }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

async function saveDonation() {
  loading.value = true
  error.value = ''
  try {
    if (editing.value) {
      await axios.put(`/dashboard/donations/${form.value.id}`, form.value)
    } else {
      await axios.post('/dashboard/donations', { ...form.value, project_id: props.projectId })
    }
    showModal.value = false
    fetchDonations()
  } catch (e) {
    error.value = 'Failed to save donation.'
  }
  loading.value = false
}

async function deleteDonation(id) {
  loading.value = true
  error.value = ''
  try {
    await axios.delete(`/dashboard/donations/${id}`)
    fetchDonations()
  } catch (e) {
    error.value = 'Failed to delete donation.'
  }
  loading.value = false
}

onMounted(fetchDonations)
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 