<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Add members as participants</h2>
      <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded flex items-center">
        <span class="text-lg mr-1">+</span> Add selection
      </button>
    </div>
    <p class="text-gray-600 mb-4">
      Members must be participants in a project to be able to collect donations. They can be added to the project individually or in groups. To select current members, we recommend assigning groups to all active members. By selecting "All Groups", they can be added to the current project.
    </p>
    <div class="flex flex-col md:flex-row gap-4 mb-4">
      <div class="bg-gray-50 border rounded p-4 w-full md:w-1/3">
        <h3 class="font-semibold mb-2">filter</h3>
        <label class="block text-sm font-medium mb-1">Search</label>
        <input v-model="search" type="text" class="input w-full mb-2" placeholder="Search" />
        <label class="flex items-center">
          <input type="checkbox" v-model="allGroups" class="mr-2" /> All groups
        </label>
      </div>
      <div class="flex-1 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg border">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">e-mail</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Groups</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="member in filteredMembers" :key="member.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ member.first_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ member.last_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ member.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ member.groups.join(', ') }}</td>
            </tr>
            <tr v-if="filteredMembers.length === 0">
              <td colspan="4" class="px-6 py-4 text-center text-gray-400">No entries found 🥺</td>
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
const allGroups = ref(false)
const members = ref([
  { id: 1, first_name: 'Anna', last_name: 'Muster', email: 'anna@example.com', groups: ['Group A'] },
  { id: 2, first_name: 'Ben', last_name: 'Beispiel', email: 'ben@example.com', groups: ['Group B'] },
  // ... more mock data
])

const filteredMembers = computed(() => {
  let list = members.value
  if (search.value) {
    const s = search.value.toLowerCase()
    list = list.filter(m =>
      m.first_name.toLowerCase().includes(s) ||
      m.last_name.toLowerCase().includes(s) ||
      m.email.toLowerCase().includes(s)
    )
  }
  // If allGroups is checked, show all, else filter by group (mock logic)
  return list
})
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 