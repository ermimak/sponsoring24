<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">Import Members</h2>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Upload File (CSV or XLSX)</label>
        <input type="file" accept=".csv,.xlsx" @change="handleFileChange" class="input w-full" />
      </div>
      <div class="flex gap-4">
        <button @click="submit" :disabled="!file || loading" class="px-4 py-2 rounded bg-purple-600 text-white font-semibold">Import</button>
        <button @click="close" class="px-4 py-2 rounded bg-gray-100 text-gray-700">Cancel</button>
      </div>
      <div v-if="error" class="text-red-600 mt-2">{{ error }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  show: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'import']);
const file = ref(null);
const error = ref('');
const loading = ref(false);

function handleFileChange(event) {
  file.value = event.target.files[0];
  error.value = '';
}

function submit() {
  if (!file.value) {
    error.value = 'Please select a file.';
    return;
  }
  loading.value = true;
  emit('import', file.value);
  loading.value = false;
}

function close() {
  file.value = null;
  error.value = '';
  emit('close');
}
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style>