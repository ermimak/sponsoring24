<template>
  <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
    <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 mb-6">Campaign Analytics</h2>
    <div v-if="loading" class="flex justify-center items-center py-8">
      <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-5 text-center shadow-sm border border-gray-100 transition-transform hover:scale-105 duration-300">
          <div class="text-3xl font-bold text-purple-700 mb-1">{{ stats.participants }}</div>
          <div class="text-gray-600 font-medium">Participants</div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 text-center shadow-sm border border-gray-100 transition-transform hover:scale-105 duration-300">
          <div class="text-3xl font-bold text-blue-700 mb-1">{{ stats.donations }}</div>
          <div class="text-gray-600 font-medium">Donations</div>
        </div>
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-5 text-center shadow-sm border border-gray-100 transition-transform hover:scale-105 duration-300">
          <div class="text-3xl font-bold text-indigo-700 mb-1">€ {{ stats.total_income }}</div>
          <div class="text-gray-600 font-medium">Total Revenue</div>
        </div>
      </div>
      <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
          <label class="text-sm font-medium text-gray-700">Campaign Progress</label>
          <span class="text-sm font-medium text-purple-700">{{ stats.progress }}% of goal</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-5 overflow-hidden">
          <div 
            class="h-5 rounded-full bg-gradient-to-r from-purple-600 to-blue-600 transition-all duration-1000 ease-out" 
            :style="{ width: stats.progress + '%' }"
          ></div>
        </div>
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