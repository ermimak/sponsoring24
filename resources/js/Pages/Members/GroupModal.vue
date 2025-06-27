<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl border border-gray-200 transform transition-all">
      <!-- Modal Header -->
      <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
        <h2 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          {{ member ? 'Edit Member Groups' : 'Manage Group' }}
        </h2>
        <button @click="close" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 rounded-full p-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Modal Content -->
      <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
          </svg>
          Group Names (comma-separated)
        </label>
        <div class="relative">
          <input
            v-model="form.name"
            type="text"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
            placeholder="Enter group names, e.g., Group1, Group2"
          />
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
        </div>
        <p class="mt-1.5 text-xs text-gray-500 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Separate multiple group names with commas
        </p>
      </div>
      
      <!-- Error Message -->
      <div v-if="error" class="text-red-600 mb-4 p-3 bg-red-50 rounded-lg flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ error }}
      </div>
      
      <!-- Modal Actions -->
      <div class="flex gap-4 justify-end">
        <button 
          @click="close" 
          class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 font-medium shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-200 flex items-center"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Cancel
        </button>
        <button 
          @click="save" 
          :disabled="loading" 
          class="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-200 flex items-center disabled:opacity-70 disabled:cursor-not-allowed"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Save
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const { show, group, member } = defineProps({
  show: { type: Boolean, default: false },
  group: { type: Object, default: null },
  member: { type: Object, default: null },
});

const emit = defineEmits(['save', 'close']);
const form = ref({ id: null, name: '' });
const error = ref('');
const loading = ref(false);

watch(
  [() => group, () => member],
  ([newGroup, newMember]) => {
    if (newMember && Array.isArray(newMember.groups)) {
      form.value = { id: null, name: newMember.groups.join(', ') };
    } else if (newGroup) {
      form.value = { ...newGroup };
    } else {
      form.value = { id: null, name: '' };
    }
    error.value = '';
  },
  { immediate: true }
);

function save() {
  const trimmedName = form.value.name.trim();
  if (!trimmedName && member) {
    // Allow empty groups for members to clear them
    emit('save', { id: null, name: '' });
    return;
  }
  if (!trimmedName) {
    error.value = 'Group names are required.';
    return;
  }
  loading.value = true;
  emit('save', form.value);
  loading.value = false;
}

function close() {
  form.value = { id: null, name: '' };
  error.value = '';
  emit('close');
}
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.375rem;
  padding: 0.5rem 0.75rem;
}
.input:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.3);
  border-color: #8b5cf6;
}
</style>