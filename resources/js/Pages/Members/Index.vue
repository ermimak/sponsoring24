<template>
  <DashboardLayout>
    <div>
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Members <span class="text-gray-400 text-base font-normal">0 entries</span></h1>
        <div class="flex gap-2 items-center">
          <button @click="redirectToGroups" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center">
            <i class="fas fa-users mr-2"></i> Member groups
          </button>
          <button @click="handleExport" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center">
            <i class="fas fa-file-export mr-2"></i> export
          </button>
          <button @click="openImportExport" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center">
            <i class="fas fa-file-import mr-2"></i> import
          </button>
          <button class="rounded-full bg-gray-100 p-2 hover:bg-gray-200"><i class="fas fa-filter text-gray-500"></i></button>
          <button @click="redirectToCreate" class="rounded-full bg-yellow-400 hover:bg-yellow-500 text-white text-2xl flex items-center justify-center w-10 h-10"><i class="fas fa-plus"></i></button>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Groups</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Public registration</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archived</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="member in filteredMembers" :key="member.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ member.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.member_id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.groups.join(', ') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.public_registration ? 'Yes' : 'No' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.email_status }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.archived ? 'Yes' : 'No' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.added }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button class="text-indigo-600 hover:text-indigo-900 mr-2"><i class="fas fa-edit"></i></button>
                  <button class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
              <tr v-if="filteredMembers.length === 0">
                <td colspan="8" class="px-6 py-4 text-center text-gray-400">No entries found 🥺</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <ImportExportModal :show="showImportExport" @close="handleImportExportClose" />
      <GroupModal :show="showGroupModal" :group="editingGroup" @save="handleGroupSave" @close="() => showGroupModal = false" />
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { router } from '@inertiajs/vue3'
import ImportExportModal from './ImportExportModal.vue'
import GroupModal from './GroupModal.vue'

const search = ref('')
const members = ref([
  // Example mock data
  // { id: 1, name: 'Anna Muster', member_id: 'M001', groups: ['Group A'], public_registration: true, email_status: 'Sent', archived: false, added: '2025-05-10' },
])

const filteredMembers = computed(() => {
  let list = members.value
  if (search.value) {
    const s = search.value.toLowerCase()
    list = list.filter(m =>
      m.name.toLowerCase().includes(s) ||
      m.member_id.toLowerCase().includes(s)
    )
  }
  return list
})

const redirectToCreate = () => {
  router.visit('/dashboard/members/create')
}

const redirectToGroups = () => {
  router.visit('/dashboard/members/groups')
}

const showImportExport = ref(false)
const showGroupModal = ref(false)
const editingGroup = ref(null)
const message = ref('')

function openImportExport() {
  showImportExport.value = true
}
function openGroupModal(group = null) {
  editingGroup.value = group
  showGroupModal.value = true
}
function handleGroupSave(group) {
  // TODO: Call API to save group, then refresh groups/members
  showGroupModal.value = false
  message.value = 'Group saved!'
}
function handleImportExportClose() {
  showImportExport.value = false
  message.value = 'Import/Export completed!'
}
function handleExport() {
  window.location = '/dashboard/members/export'
}
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 