<template>
  <div>
    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Filters</h2>
      <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select v-model="filters.status" class="input w-full">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Date From</label>
          <input v-model="filters.date_from" type="date" class="input w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Date To</label>
          <input v-model="filters.date_to" type="date" class="input w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Amount Min</label>
          <input v-model="filters.amount_min" type="number" step="0.01" class="input w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Amount Max</label>
          <input v-model="filters.amount_max" type="number" step="0.01" class="input w-full" />
        </div>
        <div class="flex items-end">
          <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">Apply Filters</button>
        </div>
      </form>
    </div>

    <!-- Donations Table -->
    <div class="bg-white shadow rounded-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">
          Donations <span class="text-gray-500">({{ donations.length }} entries)</span>
        </h2>
        <div class="space-x-2">
          <button @click="openEmailModal" :disabled="selectedDonations.length === 0" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded disabled:opacity-50">
            Mass Email
          </button>
          <button @click="generateBulkInvoice" :disabled="selectedDonations.length === 0 || isGeneratingInvoice" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded disabled:opacity-50">
            {{ isGeneratingInvoice ? 'Generating...' : 'Bulk Invoice' }}
          </button>
        </div>
      </div>

      <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg border mb-4">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donor</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="donation in donations" :key="donation.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="checkbox" :value="donation.id" v-model="selectedDonations" />
            </td>
            <td class="px-6 py-4 whitespace-nowrap">{{ donation.donor_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ donation.participant_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ donation.amount }} {{ donation.currency }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ donation.date }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="{
                'bg-green-100 text-green-800': donation.status === 'completed',
                'bg-yellow-100 text-yellow-800': donation.status === 'pending',
              }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ donation.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <Link :href="route('participant.donate.payment', { projectId: props.project.id, participantId: donation.participant_id, donationId: donation.id })" class="text-purple-600 hover:text-purple-900" title="To the invoice page">
                <i class="fas fa-file-invoice-dollar"></i>
              </Link>
            </td>
          </tr>
          <tr v-if="donations.length === 0">
            <td colspan="7" class="px-6 py-4 text-center text-gray-400">No donations found 😊</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mass Email Modal -->
    <div v-if="showEmailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Send Mass Email</h2>
        <form @submit.prevent="sendMassEmail">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Subject</label>
            <input v-model="emailForm.subject" type="text" class="input w-full" required />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Message</label>
            <textarea v-model="emailForm.message" class="input w-full" rows="4" required></textarea>
          </div>
          <div class="flex justify-end space-x-4">
            <button @click="closeEmailModal" type="button" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">Send</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Flash Messages -->
    <div v-if="flashMessage.success" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
      {{ flashMessage.success }}
    </div>
    <div v-if="flashMessage.error" class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg">
      {{ flashMessage.error }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
import axios from 'axios'
import { route } from 'ziggy-js'

const props = defineProps({
  project: Object,
  filters: Object,
  donations: Array,
})

const page = usePage()
const donations = ref([]) // Initialize as empty
const filters = ref({
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  amount_min: props.filters.amount_min || '',
  amount_max: props.filters.amount_max || '',
})
const selectedDonations = ref([])
const selectAll = ref(false)
const showEmailModal = ref(false)
const emailForm = ref({
  subject: '',
  message: '',
})
const isGeneratingInvoice = ref(false)

const flashMessage = computed(() => ({
  success: page.props.flash?.success || '',
  error: page.props.flash?.error || '',
}))

// Debounce utility
const debounce = (fn, delay) => {
  let timeoutId
  return (...args) => {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => fn(...args), delay)
  }
}

// Fetch donations without navigating away from the current route
const fetchDonations = debounce(async () => {
  try {
    const response = await axios.get(route('dashboard.project.donations.fetch', props.project.id), {
      params: {
        status: filters.value.status,
        date_from: filters.value.date_from,
        date_to: filters.value.date_to,
        amount_min: filters.value.amount_min,
        amount_max: filters.value.amount_max,
      },
    })
    donations.value = response.data.data || response.data
    selectedDonations.value = [] // Reset selections after fetching
    selectAll.value = false // Reset select all checkbox
  } catch (error) {
    console.error('Failed to fetch donations:', error)
    donations.value = []
    selectedDonations.value = []
    selectAll.value = false
  }
}, 300)

function toggleSelectAll() {
  if (selectAll.value) {
    selectedDonations.value = donations.value.map(donation => donation.id)
  } else {
    selectedDonations.value = []
  }
}

function applyFilters() {
  fetchDonations()
}

function openEmailModal() {
  showEmailModal.value = true
}

function closeEmailModal() {
  showEmailModal.value = false
  emailForm.value.subject = ''
  emailForm.value.message = ''
}

function sendMassEmail() {
  router.post(route('project.donations.massEmail', props.project.id), {
    donation_ids: selectedDonations.value,
    subject: emailForm.value.subject,
    message: emailForm.value.message,
  }, {
    onSuccess: () => {
      closeEmailModal()
      selectedDonations.value = []
      selectAll.value = false
      fetchDonations()
    },
    onError: (err) => {
      console.error('Failed to send mass email:', err);
      alert('Failed to send mass email.');
    }
  })
}

async function generateBulkInvoice() {
  if (selectedDonations.value.length === 0) return

  isGeneratingInvoice.value = true
  try {
    const response = await axios.post(route('project.donations.bulkInvoice', props.project.id), {
      donation_ids: selectedDonations.value,
    }, {
      responseType: 'blob', // Important for handling binary data (PDF)
    })

    // Create a URL for the PDF blob and trigger download
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `bulk_invoice_${props.project.id}_${new Date().toISOString().slice(0, 10)}.pdf`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)

    // Reset selections and refresh data
    selectedDonations.value = []
    selectAll.value = false
    // No need to refetch, as generating invoice doesn't change donation status in this flow
    // fetchDonations()
    alert('Bulk invoice generated successfully!');

  } catch (error) {
    console.error('Failed to generate bulk invoice:', error)
    alert('Failed to generate bulk invoice. Please try again.')
  } finally {
    isGeneratingInvoice.value = false
  }
}

onMounted(() => {
  // Always fetch data on mount based on initial filters from props or default
  fetchDonations();
})

// Watch project changes, but avoid infinite loops
watch(() => props.project.id, (newProjectId) => {
  if (newProjectId) {
    fetchDonations()
  }
})

// Watch filters, but only fetch if values actually change (deep watch)
watch(filters, (newFilters, oldFilters) => {
  // Deep compare to avoid unnecessary fetches
  if (JSON.stringify(newFilters) !== JSON.stringify(oldFilters)) {
    fetchDonations();
  }
}, { deep: true });

// Watch for changes in the initial donations prop if it's provided later (less common for initial load)
// watch(() => props.donations, (newDonations) => {
//   if (newDonations && newDonations.length > 0) {
//     donations.value = newDonations;
//   }
// }, { immediate: true }); // Use immediate to potentially load initial prop data

</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 w-full;
}
</style>