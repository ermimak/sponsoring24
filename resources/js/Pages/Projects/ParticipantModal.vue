<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg">
      <h2 class="text-xl font-bold mb-4">Add Members as Participants</h2>
      <form @submit.prevent="handleSave">
        <label class="block mb-2 font-medium">Select Members</label>
        <select v-model="selected" multiple class="input w-full mb-4" size="8">
          <option v-for="member in members" :key="member.id" :value="member.id">
            {{ member.first_name }} {{ member.last_name }} ({{ member.email }})
          </option>
        </select>
        <div class="flex gap-2 justify-end">
          <button type="button" @click="$emit('close')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
          <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded">Add</button>
        </div>
      </form>
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">&times;</button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({ show: Boolean, members: Array, projectId: Number })
const emit = defineEmits(['save', 'close'])
const selected = ref([])

watch(() => props.show, (val) => {
  if (val) selected.value = []
})

function handleSave() {
  emit('save', selected.value)
}
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 