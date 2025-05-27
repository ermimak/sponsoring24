<template>
  <DashboardLayout>
    <div>
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Member Groups <span class="text-gray-400 text-base font-normal">{{ groups.length }} groups</span></h1>
        <div class="flex gap-2 items-center">
          <button @click="openCreateModal" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Group
          </button>
          <button @click="router.visit('/dashboard/members')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center">
            <i class="fas fa-users mr-2"></i> Back to Members
          </button>
        </div>
      </div>
      <div class="bg-white rounded-lg border">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="group in groups" :key="group.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ group.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ group.member_count }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button @click="deleteGroup(group)" class="text-red-600 hover:text-red-900 flex items-center gap-1" :disabled="loading">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </td>
            </tr>
            <tr v-if="groups.length === 0">
              <td colspan="3" class="px-6 py-4 text-center text-gray-400">No groups found 🥺</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="loading" class="p-4 text-center text-gray-500">Loading...</div>
      <div v-if="error" class="p-4 text-center text-red-500">{{ error }}</div>
      <div v-if="message" class="p-4 text-center text-green-500">{{ message }}</div>
      <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          <h2 class="text-xl font-bold mb-4">Create Group</h2>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Group Name</label>
            <input v-model="newGroupName"
type="text"
class="input w-full"
placeholder="Enter group name" />
          </div>
          <div class="flex gap-4">
            <button @click="createGroup" class="px-4 py-2 rounded bg-purple-600 text-white font-semibold" :disabled="loading">Save</button>
            <button @click="showCreateModal = false" class="px-4 py-2 rounded bg-gray-100 text-gray-700">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const groups = ref([]);
const loading = ref(false);
const error = ref('');
const message = ref('');
const showCreateModal = ref(false);
const newGroupName = ref('');

async function fetchGroups() {
  try {
    loading.value = true;
    const response = await axios.get('/dashboard/members/groups/data');
    groups.value = response.data;
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to load groups.';
  } finally {
    loading.value = false;
  }
}

async function createGroup() {
  if (!newGroupName.value.trim()) {
    error.value = 'Group name is required.';
    return;
  }
  try {
    loading.value = true;
    await axios.post('/dashboard/members/groups', { name: newGroupName.value.trim() });
    message.value = 'Group created successfully!';
    newGroupName.value = '';
    showCreateModal.value = false;
    await fetchGroups();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to create group.';
  } finally {
    loading.value = false;
  }
}

async function deleteGroup(group) {
  if (!confirm(`Are you sure you want to delete the group "${group.name}"? This will remove it from all members.`)) return;
  try {
    loading.value = true;
    await axios.delete(`/dashboard/members/groups/${group.id}`);
    message.value = 'Group deleted successfully!';
    await fetchGroups();
  } catch (e) {
    error.value = e.response?.status === 404
      ? 'Group not found.'
      : e.response?.status === 403
      ? 'You do not have permission to delete this group.'
      : e.response?.data?.message || 'Failed to delete group.';
  } finally {
    loading.value = false;
  }
}

function openCreateModal() {
  newGroupName.value = '';
  error.value = '';
  showCreateModal.value = true;
}

onMounted(() => {
  fetchGroups();
});
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style>