<template>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <h1 class="text-3xl font-bold text-gray-900">
            Donations for {{ project.name[Object.keys(project.name)[0]] || 'Unnamed Project' }}
          </h1>
        </div>
      </header>
  
      <!-- Main Content -->
      <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Filters</h2>
          <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Status</label>
              <select v-model="filters.status" class="w-full p-2 border border-gray-300 rounded-lg">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Date From</label>
              <input v-model="filters.date_from" type="date" class="w-full p-2 border border-gray-300 rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Date To</label>
              <input v-model="filters.date_to" type="date" class="w-full p-2 border border-gray-300 rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Amount Min</label>
              <input v-model="filters.amount_min"
type="number"
step="0.01"
class="w-full p-2 border border-gray-300 rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Amount Max</label>
              <input v-model="filters.amount_max"
type="number"
step="0.01"
class="w-full p-2 border border-gray-300 rounded-lg" />
            </div>
            <div class="flex items-end">
              <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">Apply Filters</button>
            </div>
          </form>
        </div>
  
        <!-- Donations Table -->
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Donations</h2>
            <div class="space-x-4">
              <button @click="openEmailModal" :disabled="selectedDonations.length === 0" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 disabled:bg-gray-400">
                Mass Email
              </button>
              <button @click="generateBulkInvoice" :disabled="selectedDonations.length === 0" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 disabled:bg-gray-400">
                Bulk Invoice
              </button>
            </div>
          </div>
  
          <table class="min-w-full divide-y divide-gray-200" v-if="donations.length > 0">
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
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="donation in donations" :key="donation.id">
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
                  }"
class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ donation.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-else class="text-center py-10 text-gray-500">
            No donations found 😊
          </div>
        </div>
  
        <!-- Mass Email Modal -->
        <div v-if="showEmailModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Send Mass Email</h2>
            <form @submit.prevent="sendMassEmail">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Subject</label>
                <input v-model="emailForm.subject"
type="text"
class="w-full p-2 border border-gray-300 rounded-lg"
required />
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Message</label>
                <textarea v-model="emailForm.message"
class="w-full p-2 border border-gray-300 rounded-lg"
rows="4"
required></textarea>
              </div>
              <div class="flex justify-end space-x-4">
                <button @click="closeEmailModal" type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">Send</button>
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
      </main>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch, computed } from 'vue'
  import { router, usePage } from '@inertiajs/vue3'
  
  const props = defineProps({
    project: Object,
    filters: Object,
  })
  
  const page = usePage()
  const donations = ref([])
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
  
  const flashMessage = computed(() => ({
    success: page.props.flash?.success || '',
    error: page.props.flash?.error || '',
  }))
  
  function fetchDonations() {
    router.get(route('project.donations.index', props.project.id), {
      status: filters.value.status,
      date_from: filters.value.date_from,
      date_to: filters.value.date_to,
      amount_min: filters.value.amount_min,
      amount_max: filters.value.amount_max,
    }, {
      preserveState: true,
      onSuccess: (page) => {
        donations.value = page.props.donations || []
      },
    })
  }
  
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
    })
  }
  
  function generateBulkInvoice() {
    router.post(route('project.donations.bulkInvoice', props.project.id), {
      donation_ids: selectedDonations.value,
    }, {
      onSuccess: () => {
        selectedDonations.value = []
        selectAll.value = false
        fetchDonations()
      },
    })
  }
  
  onMounted(() => {
    fetchDonations()
  })
  
  watch(() => props.project, (newProject) => {
    if (newProject) {
      fetchDonations()
    }
  })
  
  watch(filters, () => {
    fetchDonations()
  }, { deep: true })
  </script>