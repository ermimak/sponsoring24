<template>
  <DashboardLayout>
    <div>
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Member groups <span class="text-gray-400 text-base font-normal">0 entries</span></h1>
        <div class="flex gap-2">
          <button class="rounded-full bg-gray-100 p-2 hover:bg-gray-200"><i class="fas fa-filter text-gray-500"></i></button>
          <button @click="openGroupModal()" class="rounded-full bg-yellow-400 hover:bg-yellow-500 text-white text-2xl flex items-center justify-center w-10 h-10"><i class="fas fa-plus"></i></button>
        </div>
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
      <GroupModal :show="showGroupModal" :group="editingGroup" @save="handleGroupSave" @close="() => showGroupModal = false" />
      <div v-if="message" class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        {{ message }}
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { router } from '@inertiajs/vue3'
import GroupModal from './GroupModal.vue'

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

const redirectToCreate = () => {
  router.visit('/members/groups/create')
}

const showGroupModal = ref(false)
const editingGroup = ref(null)
const message = ref('')

function openGroupModal(group = null) {
  editingGroup.value = group
  showGroupModal.value = true
}

function handleGroupSave(group) {
  // TODO: Call API to save group, then refresh groups
  showGroupModal.value = false
  message.value = 'Group saved!'
}
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 