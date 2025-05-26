<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Donations</h2>
      <div class="flex gap-2">
        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">Mass email</button>
        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">Bulk invoice (PDF)</button>
        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">export</button>
      </div>
    </div>
    <div class="flex flex-col md:flex-row gap-4 mb-4">
      <div class="bg-gray-50 border rounded p-4 w-full md:w-1/4">
        <label class="block text-sm font-medium mb-1">Search</label>
        <input v-model="search"
type="text"
class="input w-full mb-2"
placeholder="Search" />
      </div>
      <div class="flex-1 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg border">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">team</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supporters</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">donation</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales volume</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billing date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emails</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="donation in filteredDonations" :key="donation.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ donation.type }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ donation.participant }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ donation.team }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ donation.supporters }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ donation.donation }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ donation.sales_volume }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ donation.billing_date }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ donation.emails }}</td>
            </tr>
            <tr v-if="filteredDonations.length === 0">
              <td colspan="8" class="px-6 py-4 text-center text-gray-400">No entries found 🥺</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const search = ref('')
const donations = ref([
  { id: 1, type: 'Flat', participant: 'Anna Muster', team: 'Team 1', supporters: 2, donation: 'CHF 20.00', sales_volume: 'CHF 20.00', billing_date: '2025-12-25', emails: 1 },
  // ... more mock data
])

const filteredDonations = computed(() => {
  let list = donations.value
  if (search.value) {
    const s = search.value.toLowerCase()
    list = list.filter(d =>
      d.type.toLowerCase().includes(s) ||
      d.participant.toLowerCase().includes(s) ||
      d.team.toLowerCase().includes(s)
    )
  }
  return list
})
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 