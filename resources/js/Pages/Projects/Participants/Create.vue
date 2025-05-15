<template>
    <DashboardLayout>
      <div class="max-w-5xl mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold">Add Participants to Project</h1>
          <button @click="cancel" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
            Back to Participants
          </button>
        </div>
        <div v-if="loading" class="text-center text-gray-500">Loading groups...</div>
        <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
        <div v-else>
          <div class="bg-gray-50 rounded-lg p-6 border space-y-6">
            <div>
              <h2 class="text-lg font-semibold mb-2">Select Groups to Add Participants</h2>
              <p class="text-gray-600 mb-4">
                Select one or more groups to add their members as participants to this project. All members of the selected groups will be added.
              </p>
              <label class="block text-sm font-medium text-gray-700 mb-1">Groups*</label>
              <select v-model="selectedGroups" multiple class="input w-full" required>
                <option v-for="group in groups" :key="group.id" :value="group.id">{{ group.name }}</option>
              </select>
              <p class="text-sm text-gray-600 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple groups.</p>
            </div>
            <div v-if="selectedGroups.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <h3 class="text-sm font-medium text-blue-800">Selected Groups:</h3>
              <ul class="list-disc list-inside text-blue-600">
                <li v-for="groupId in selectedGroups" :key="groupId">
                  {{ groups.find(g => g.id === groupId)?.name }}
                </li>
              </ul>
            </div>
            <div class="flex justify-end gap-4">
              <button type="button" @click="cancel" class="px-6 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                Cancel
              </button>
              <button @click="addParticipants" type="submit" class="px-6 py-2 rounded bg-purple-600 text-white font-semibold hover:bg-purple-700 transition" :disabled="loading || selectedGroups.length === 0">
                {{ loading ? 'Adding...' : 'Add Participants' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
  </template>
<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({ projectId: String })

const groups = ref([])
const selectedGroups = ref([])
const loading = ref(false)
const error = ref('')

async function fetchGroups() {
  loading.value = true
  error.value = ''
  try {
    const response = await axios.get('/dashboard/members/groups/data')
    groups.value = response.data.map(group => ({
      id: group.id,
      name: group.name,
      member_count: group.member_count || 0
    }))
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to load groups.'
  } finally {
    loading.value = false
  }
}

async function addParticipants() {
    if (selectedGroups.value.length === 0) return

    loading.value = true
    error.value = ''
    try {
        await axios.post(`/dashboard/projects/${props.projectId}/participants`, { group_ids: selectedGroups.value })
        router.visit(`/dashboard/projects/${props.projectId}/edit?tab=Participants`, {
            onSuccess: () => {
                alert('Participants added successfully!')
                window.location.href = `/dashboard/projects/${props.projectId}/edit?tab=Participants`
            }
        })
    } catch (e) {
        error.value = 'Failed to add participants: ' + (e.response?.data?.error || e.message)
    } finally {
        loading.value = false
    }
}

function cancel() {
  router.visit(`/dashboard/projects/${props.projectId}/edit?tab=Participants`)
}

onMounted(() => {
  fetchGroups()
})
</script>
  
  <style scoped>
  .input {
    @apply border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 w-full;
  }
  </style>