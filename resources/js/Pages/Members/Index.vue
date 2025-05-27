<template>
  <DashboardLayout>
    <div>
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Members <span class="text-gray-400 text-base font-normal">{{ filteredMembers.length }} entries</span></h1>
        <div class="flex gap-2 items-center">
          <button @click="redirectToGroups" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center" :disabled="loading">
            <i class="fas fa-users mr-2"></i> Member groups
          </button>
          <button @click="handleExport" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center" :disabled="exporting">
            <i class="fas fa-file-export mr-2"></i> {{ exporting ? 'Exporting...' : 'Export' }}
          </button>
          <button @click="openImportExport" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center">
            <i class="fas fa-file-import mr-2"></i> Import
          </button>
          <button class="rounded-full bg-gray-100 p-2 hover:bg-gray-200"><i class="fas fa-filter text-gray-500"></i></button>
          <button @click="redirectToCreate" class="rounded-full bg-yellow-400 hover:bg-yellow-500 text-white text-2xl flex items-center justify-center w-10 h-10"><i class="fas fa-plus"></i></button>
        </div>
      </div>
      <div class="flex flex-col md:flex-row gap-4 mb-4">
        <div class="bg-gray-50 border rounded p-4 w-full md:w-1/4">
          <label class="block text-sm font-medium mb-1">Search</label>
          <input v-model="search"
type="text"
class="input w-full mb-2"
placeholder="Search" />
        </div>
        <div class="flex-1 overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg border">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Groups</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Public Registration</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archived</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="member in filteredMembers" :key="member.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <button @click="handleEditMember(member)" class="text-indigo-600 hover:text-indigo-900 font-medium">{{ member.name }}</button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.member_id || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button @click="openGroupModal(member)" class="text-indigo-600 hover:text-indigo-900">{{ (member.groups || []).join(', ') || 'None' }}</button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.public_registration ? 'Yes' : 'No' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.email_status || 'OK' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.archived ? 'Yes' : 'No' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ member.added || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex items-center gap-2">
                  <button @click="handleEditMember(member)" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                  <button @click="handleMemberDelete(member)" class="text-red-600 hover:text-red-900 flex items-center gap-1" :disabled="loading">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </td>
              </tr>
              <tr v-if="filteredMembers.length === 0">
                <td colspan="8" class="px-6 py-4 text-center text-gray-400">No entries found 🥺</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <ImportExportModal :show="showImportExport" @close="handleImportExportClose" @import="handleImport" />
      <GroupModal :show="showGroupModal"
:group="editingGroup"
:member="editingMember"
@save="handleGroupSave"
@close="() => showGroupModal = false" />
      <div v-if="loading" class="p-4 text-center text-gray-500">Loading...</div>
      <div v-if="error" class="p-4 text-center text-red-500">{{ error }}</div>
      <div v-if="message" class="p-4 text-center text-green-500">{{ message }}</div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { router } from '@inertiajs/vue3';
import ImportExportModal from './ImportExportModal.vue';
import GroupModal from './GroupModal.vue';
import axios from 'axios';

const props = defineProps({ members: { type: Array, default: () => [] } });
const search = ref('');
const members = ref([]);
const loading = ref(false);
const exporting = ref(false);
const error = ref('');
const message = ref('');
const showImportExport = ref(false);
const showGroupModal = ref(false);
const editingGroup = ref(null);
const editingMember = ref(null);

const filteredMembers = computed(() => {
  let list = members.value;
  if (search.value) {
    const s = search.value.toLowerCase();
    list = list.filter(m =>
      (m.name || '').toLowerCase().includes(s) ||
      (m.member_id || '').toLowerCase().includes(s)
    );
  }
  return list;
});

const redirectToCreate = () => {
  router.visit('/dashboard/members/create');
};

const redirectToGroups = async () => {
  try {
    loading.value = true;
    await router.visit('/dashboard/members/groups', { preserveState: true });
  } catch (e) {
    console.error('Failed to navigate to groups:', e);
    error.value = 'Failed to load groups page. Please try again.';
  } finally {
    loading.value = false;
  }
};

function openImportExport() {
  showImportExport.value = true;
}

function openGroupModal(member = null) {
  editingMember.value = member;
  editingGroup.value = member ? { id: null, name: Array.isArray(member.groups) ? member.groups.join(', ') : '' } : null;
  showGroupModal.value = true;
}

async function handleGroupSave(data) {
  try {
    if (editingMember.value) {
      const groups = data.name ? data.name.split(',').map(name => name.trim()).filter(name => name) : [];
      await axios.put(`/dashboard/members/${editingMember.value.id}`, { groups });
      message.value = 'Member groups updated!';
    } else {
      await axios.post(`/dashboard/members/groups`, { name: data.name });
      message.value = 'Group saved!';
    }
    showGroupModal.value = false;
    editingMember.value = null;
    editingGroup.value = null;
    fetchMembers();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to save groups.';
  }
}

async function handleImport(file) {
  const formData = new FormData();
  formData.append('file', file);
  try {
    const response = await axios.post('/dashboard/members/import', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    message.value = response.data.message;
    showImportExport.value = false;
    fetchMembers();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to import members.';
  }
}

function handleImportExportClose() {
  showImportExport.value = false;
}

async function handleExport() {
  try {
    exporting.value = true;
    const response = await axios.get('/dashboard/members/export', {
      responseType: 'blob',
    });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'participants.xlsx');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    message.value = 'Export successful!';
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to export members.';
  } finally {
    exporting.value = false;
  }
}

async function fetchMembers() {
  loading.value = true;
  error.value = '';
  message.value = '';
  try {
    const response = await axios.get('/dashboard/members');
    members.value = response.data.map(m => ({
      id: m.id,
      name: `${m.first_name || ''} ${m.last_name || ''}`.trim() || 'N/A',
      member_id: m.member_id || 'N/A',
      groups: Array.isArray(m.member_groups) ? m.member_groups.map(g => g.name) : [],
      public_registration: !!m.public_registration,
      email_status: m.email_status || 'OK',
      archived: !!m.archived,
      added: m.created_at ? new Date(m.created_at).toLocaleDateString() + ' ' + new Date(m.created_at).toLocaleTimeString().slice(0, 5) : 'N/A',
    }));
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to load members.';
  } finally {
    loading.value = false;
  }
}

async function handleMemberDelete(member) {
  if (!confirm(`Are you sure you want to delete ${member.name || 'this member'}?`)) return;
  try {
    loading.value = true;
    await axios.delete(`/dashboard/members/${member.id}`);
    message.value = 'Member deleted successfully!';
    await fetchMembers();
  } catch (e) {
    error.value = e.response?.status === 404
      ? 'Member not found.'
      : e.response?.status === 403
      ? 'You do not have permission to delete this member.'
      : e.response?.data?.message || 'Failed to delete member.';
  } finally {
    loading.value = false;
  }
}

function handleEditMember(member) {
  router.visit(`/dashboard/members/${member.id}/edit`);
}

onMounted(() => {
  if (props.members.length > 0) {
    members.value = props.members.map(m => ({
      id: m.id,
      name: `${m.first_name || ''} ${m.last_name || ''}`.trim() || 'N/A',
      member_id: m.member_id || 'N/A',
      groups: Array.isArray(m.member_groups) ? m.member_groups.map(g => g.name) : [],
      public_registration: !!m.public_registration,
      email_status: m.email_status || 'OK',
      archived: !!m.archived,
      added: m.created_at ? new Date(m.created_at).toLocaleDateString() + ' ' + new Date(m.created_at).toLocaleTimeString().slice(0, 5) : 'N/A',
    }));
  } else {
    fetchMembers();
  }
});
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style>