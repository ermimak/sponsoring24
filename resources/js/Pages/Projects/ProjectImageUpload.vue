<template>
  <div class="bg-white rounded-lg p-6 border">
    <h2 class="text-xl font-semibold mb-4">Project Images</h2>
    <form @submit.prevent="uploadImages">
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Landscape Image (1200x300px)</label>
        <input type="file" @change="onFileChange($event, 'landscape')" accept="image/*" />
        <img v-if="preview.landscape" :src="preview.landscape" class="mt-2 rounded border w-full max-w-lg" />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Square Image (400x400px)</label>
        <input type="file" @change="onFileChange($event, 'square')" accept="image/*" />
        <img v-if="preview.square" :src="preview.square" class="mt-2 rounded border w-40 h-40 object-cover" />
      </div>
      <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded" :disabled="loading">Upload</button>
    </form>
    <div v-if="message" class="mt-4 text-green-600">{{ message }}</div>
    <div v-if="loading" class="text-gray-500">Uploading...</div>
    <div v-if="error" class="text-red-600">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({ projectId: Number })
const files = ref({ landscape: null, square: null })
const preview = ref({ landscape: '', square: '' })
const message = ref('')
const loading = ref(false)
const error = ref('')

function onFileChange(e, type) {
  const file = e.target.files[0]
  files.value[type] = file
  if (file) {
    const reader = new FileReader()
    reader.onload = (ev) => { preview.value[type] = ev.target.result }
    reader.readAsDataURL(file)
  }
}

async function uploadImages() {
  loading.value = true
  error.value = ''
  try {
    const formData = new FormData()
    if (files.value.landscape) formData.append('image_landscape', files.value.landscape)
    if (files.value.square) formData.append('image_square', files.value.square)
    await axios.post(`/dashboard/projects/${props.projectId}/upload-image`, formData)
    message.value = 'Images uploaded successfully!'
  } catch (e) {
    error.value = 'Failed to upload images.'
  }
  loading.value = false
}
</script> 