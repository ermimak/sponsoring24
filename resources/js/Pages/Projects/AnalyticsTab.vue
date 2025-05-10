<template>
  <div class="bg-white rounded-lg p-6 border">
    <h2 class="text-xl font-semibold mb-4">Project Analytics</h2>
    <div v-if="loading" class="text-gray-500">Loading...</div>
    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gray-50 rounded p-4 text-center">
          <div class="text-2xl font-bold">{{ stats.participants }}</div>
          <div class="text-gray-600">Participants</div>
        </div>
        <div class="bg-gray-50 rounded p-4 text-center">
          <div class="text-2xl font-bold">{{ stats.donations }}</div>
          <div class="text-gray-600">Donations</div>
        </div>
        <div class="bg-gray-50 rounded p-4 text-center">
          <div class="text-2xl font-bold">CHF {{ stats.total_income }}</div>
          <div class="text-gray-600">Total Income</div>
        </div>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Progress</label>
        <div class="w-full bg-gray-200 rounded-full h-4">
          <div class="bg-purple-600 h-4 rounded-full" :style="{ width: stats.progress + '%' }"></div>
        </div>
        <div class="text-xs text-gray-500 mt-1">{{ stats.progress }}% of goal reached</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({ projectId: String })
const stats = ref({ participants: 0, donations: 0, total_income: 0, progress: 0 })
const loading = ref(true)

async function fetchAnalytics() {
  loading.value = true
  // Replace with real endpoint
  const { data } = await axios.get(`/dashboard/projects/${props.projectId}/analytics`)
  stats.value = data
  loading.value = false
}

onMounted(fetchAnalytics)
</script> 