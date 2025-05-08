<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">Import/Export Members</h2>
      <form @submit.prevent="handleImport" class="mb-4">
        <label class="block mb-2 font-medium">Import Members (CSV/XLSX)</label>
        <input type="file" @change="onFileChange" accept=".csv,.xlsx" class="mb-4" />
        <button type="submit" :disabled="!file" class="bg-purple-600 text-white px-4 py-2 rounded">Import</button>
      </form>
      <div class="mb-4">
        <button @click="handleExport" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Export Members</button>
      </div>
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">&times;</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({ show: Boolean })
const emit = defineEmits(['close'])
const file = ref(null)

function onFileChange(e) {
  file.value = e.target.files[0]
}

async function handleImport() {
  if (!file.value) return
  const formData = new FormData()
  formData.append('file', file.value)
  await axios.post('/dashboard/members/import', formData)
  emit('close')
}

function handleExport() {
  window.location = '/dashboard/members/export'
}
</script> 