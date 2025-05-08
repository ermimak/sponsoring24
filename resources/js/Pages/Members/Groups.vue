<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Member groups <span class="text-gray-400 text-base font-normal">0 entries</span></h1>
      <button class="bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2 rounded-full text-2xl flex items-center justify-center">+</button>
    </div>
    <div class="flex flex-col md:flex-row gap-4 mb-4">
      <div class="bg-gray-50 border rounded p-4 w-full md:w-1/4">
        <label class="block text-sm font-medium mb-1">Search</label>
        <input v-model="search" type="text" class="input w-full mb-2" placeholder="Search" />
      </div>
      <div class="flex-1 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg border">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="group in filteredGroups" :key="group.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ group.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ group.members }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ group.created }}</td>
            </tr>
            <tr v-if="filteredGroups.length === 0">
              <td colspan="3" class="px-6 py-4 text-center text-gray-400">No entries found 🥺</td>
            </tr>
          </tbody>
        </table>
        <div class="flex justify-between items-center mt-4">
          <span class="text-sm text-gray-500">Page 1 of 1 | 0 entries total</span>
          <div class="flex gap-2">
            <button class="px-3 py-1 rounded bg-gray-100 text-gray-500" disabled>&lt;</button>
            <button class="px-3 py-1 rounded bg-gray-100 text-gray-500" disabled>&gt;</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const search = ref('')
const groups = ref([
  // Example mock data
  // { id: 1, name: 'Group A', members: 5, created: '2025-05-10' },
])

const filteredGroups = computed(() => {
  let list = groups.value
  if (search.value) {
    const s = search.value.toLowerCase()
    list = list.filter(g =>
      g.name.toLowerCase().includes(s)
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