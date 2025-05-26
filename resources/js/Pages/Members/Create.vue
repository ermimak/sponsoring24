<template>
  <DashboardLayout>
    <div>
      <h1 class="text-2xl font-bold mb-6">{{ editingId ? 'Edit Member' : 'Add Member' }}</h1>
      <form @submit.prevent="submit" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Profile -->
          <div class="bg-gray-50 rounded-lg p-6 border col-span-2">
            <h2 class="font-semibold text-lg mb-4">Profile</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">Gender{{ editingId ? '' : '*' }}</label>
                <select v-model="form.gender" class="input w-full" :required="!editingId">
                  <option value="">Please select...</option>
                  <option value="female">Female</option>
                  <option value="male">Male</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Company</label>
                <input v-model="form.company" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">First Name{{ editingId ? '' : '*' }}</label>
                <input v-model="form.first_name"
type="text"
class="input w-full"
:required="!editingId" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Last Name{{ editingId ? '' : '*' }}</label>
                <input v-model="form.last_name"
type="text"
class="input w-full"
:required="!editingId" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Address</label>
                <input v-model="form.address" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Address Suffix</label>
                <input v-model="form.address_suffix" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Postal Code</label>
                <input v-model="form.postal_code" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Location</label>
                <input v-model="form.location" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Country</label>
                <select v-model="form.country" class="input w-full">
                  <option value="">Select Country</option>
                  <option value="Switzerland">Switzerland</option>
                  <option value="Germany">Germany</option>
                  <option value="France">France</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Birthday</label>
                <input v-model="form.birthday" type="date" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Member ID</label>
                <input v-model="form.member_id" type="text" class="input w-full" />
              </div>
            </div>
          </div>
          <!-- Contact & Groups -->
          <div class="flex flex-col gap-6">
            <div class="bg-gray-50 rounded-lg p-6 border">
              <h2 class="font-semibold text-lg mb-4">Contact</h2>
              <div class="mb-2">
                <label class="block text-sm font-medium mb-1">E-mail{{ editingId ? '' : '*' }}</label>
                <input v-model="form.email"
type="email"
class="input w-full"
:required="!editingId" />
              </div>
              <div class="mb-2">
                <label class="block text-sm font-medium mb-1">Email CC</label>
                <input v-model="form.email_cc" type="email" class="input w-full" />
              </div>
              <div class="mb-2">
                <label class="block text-sm font-medium mb-1">Phone</label>
                <input v-model="form.phone" type="text" class="input w-full" />
              </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-6 border">
              <h2 class="font-semibold text-lg mb-4">Member Groups</h2>
              <label class="block text-sm font-medium mb-1">Member Groups</label>
              <select v-model="form.groups" class="input w-full" multiple>
                <option v-for="group in allGroups" :key="group.id" :value="group.name">{{ group.name }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-1">Select one or more groups (Ctrl+click for multiple).</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-6 border">
              <h2 class="font-semibold text-lg mb-4">Settings</h2>
              <label class="flex items-center">
                <input type="checkbox" v-model="form.archived" class="mr-2" /> Archived
              </label>
            </div>
          </div>
        </div>
        <div v-if="error" class="text-red-600 mb-2">{{ error }}</div>
        <div v-if="loading" class="text-gray-500 mb-2">Loading member data...</div>
        <div class="flex gap-4 mt-8">
          <button type="submit" :disabled="loading" class="px-6 py-2 rounded bg-purple-600 text-white font-semibold">
            {{ loading ? 'Saving...' : 'Save' }}
          </button>
          <button type="button"
@click="saveAndClose"
:disabled="loading"
class="px-6 py-2 rounded bg-purple-600 text-white font-semibold">
            {{ loading ? 'Saving...' : 'Save and Close' }}
          </button>
          <button type="button" @click="cancel" class="px-6 py-2 rounded bg-gray-100 text-gray-700">Cancel</button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  routeParams: {
    type: Object,
    default: () => ({}),
  },
});

const editingId = ref(null);
const allGroups = ref([]);
const form = ref({
  gender: '',
  company: '',
  first_name: '',
  last_name: '',
  address: '',
  address_suffix: '',
  postal_code: '',
  location: '',
  country: '',
  birthday: '',
  member_id: '',
  email: '',
  email_cc: '',
  phone: '',
  groups: [],
  archived: false,
});
const error = ref('');
const loading = ref(false);

async function fetchGroups() {
  try {
    const response = await axios.get('/dashboard/members/groups/data');
    allGroups.value = response.data.map(g => ({ id: g.id, name: g.name }));
  } catch (e) {
    error.value = 'Failed to load groups. Please try again.';
    allGroups.value = [];
  }
}

async function fetchMember() {
  if (!editingId.value || isNaN(editingId.value)) {
    error.value = 'Invalid member ID. Please select a valid member to edit.';
    return;
  }
  try {
    loading.value = true;
    const response = await axios.get(`/dashboard/members/${editingId.value}`);
    const member = response.data;
    if (!member.id) {
      error.value = 'Member not found. Please select a valid member.';
      return;
    }
    form.value = {
      gender: member.gender || '',
      first_name: member.first_name || '',
      last_name: member.last_name || '',
      company: member.company || '',
      address: member.address || '',
      address_suffix: member.address_suffix || '',
      postal_code: member.postal_code || '',
      location: member.location || '',
      country: member.country || '',
      birthday: member.birthday || '',
      member_id: member.member_id || '',
      email: member.email || '',
      email_cc: member.email_cc || '',
      phone: member.phone || '',
      groups: Array.isArray(member.member_groups) ? member.member_groups.map(g => g.name) : [],
      archived: !!member.archived,
    };
    console.log('Fetched member data:', member);
  } catch (e) {
    console.error('Failed to fetch member:', e.response?.data || e.message);
    error.value = e.response?.status === 404 
      ? 'Member not found. Please select a valid member.'
      : e.response?.data?.message || 'Failed to load member data. Please try again.';
  } finally {
    loading.value = false;
  }
}

async function submit() {
  loading.value = true;
  error.value = '';
  try {
    const payload = {
      ...form.value,
      groups: Array.isArray(form.value.groups) ? form.value.groups : [],
    };
    if (editingId.value) {
      await axios.put(`/dashboard/members/${editingId.value}`, payload);
    } else {
      await axios.post('/dashboard/members', payload);
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to save member.';
  } finally {
    loading.value = false;
  }
}

async function saveAndClose() {
  await submit();
  if (!error.value) {
    router.visit('/dashboard/members');
  }
}

function cancel() {
  router.visit('/dashboard/members');
}

onMounted(() => {
  fetchGroups();
  if (props.routeParams.id && !isNaN(props.routeParams.id)) {
    editingId.value = props.routeParams.id;
    fetchMember();
  } else if (props.routeParams.id) {
    error.value = 'Invalid member ID. Loading create form instead.';
  }
});
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style>