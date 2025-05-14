<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">{{ member ? 'Edit Member Groups' : 'Manage Group' }}</h2>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Group Names (comma-separated)</label>
        <input
          v-model="form.name"
          type="text"
          class="input w-full"
          placeholder="Enter group names, e.g., Group1, Group2"
        />
      </div>
      <div class="flex gap-4">
        <button @click="save" class="px-4 py-2 rounded bg-purple-600 text-white font-semibold" :disabled="loading">Save</button>
        <button @click="close" class="px-4 py-2 rounded bg-gray-100 text-gray-700">Cancel</button>
      </div>
      <div v-if="error" class="text-red-600 mt-2">{{ error }}</div>
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
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style>