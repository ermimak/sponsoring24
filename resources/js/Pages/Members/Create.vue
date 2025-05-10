<template>
  <DashboardLayout>
    <div>
      <h1 class="text-2xl font-bold mb-6">Add member</h1>
      <form @submit.prevent="submit" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Profile -->
          <div class="bg-gray-50 rounded-lg p-6 border col-span-2">
            <h2 class="font-semibold text-lg mb-4">profile</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">Gender*</label>
                <select v-model="form.gender" class="input w-full" required>
                  <option value="">Please select...</option>
                  <option value="female">Female</option>
                  <option value="male">Male</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">company</label>
                <input v-model="form.company" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">First name*</label>
                <input v-model="form.first_name" type="text" class="input w-full" required />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Last name*</label>
                <input v-model="form.last_name" type="text" class="input w-full" required />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">address</label>
                <input v-model="form.address" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Address suffix</label>
                <input v-model="form.address_suffix" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Postal code</label>
                <input v-model="form.postal_code" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Location</label>
                <input v-model="form.location" type="text" class="input w-full" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">country</label>
                <select v-model="form.country" class="input w-full">
                  <option value="Switzerland">Switzerland</option>
                  <option value="Germany">Germany</option>
                  <option value="France">France</option>
                  <!-- Add more countries as needed -->
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
              <h2 class="font-semibold text-lg mb-4">contact</h2>
              <div class="mb-2">
                <label class="block text-sm font-medium mb-1">E-mail*</label>
                <input v-model="form.email" type="email" class="input w-full" required />
              </div>
              <div class="mb-2">
                <label class="block text-sm font-medium mb-1">Email CC</label>
                <input v-model="form.email_cc" type="email" class="input w-full" />
              </div>
              <div class="mb-2">
                <label class="block text-sm font-medium mb-1">phone</label>
                <input v-model="form.phone" type="text" class="input w-full" />
              </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-6 border">
              <h2 class="font-semibold text-lg mb-4">Member groups</h2>
              <label class="block text-sm font-medium mb-1">Member groups</label>
              <select v-model="form.groups" class="input w-full" multiple>
                <option v-for="group in allGroups" :key="group.id" :value="group.name">{{ group.name }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-1">Select one or more groups or enter a new name to add a new group.</p>
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
        <div class="flex gap-4 mt-8">
          <button type="submit" :disabled="loading" class="px-6 py-2 rounded bg-purple-600 text-white font-semibold">Save</button>
          <button type="button" @click="saveAndClose" :disabled="loading" class="px-6 py-2 rounded bg-purple-600 text-white font-semibold">Save and close</button>
          <button type="button" @click="cancel" class="px-6 py-2 rounded bg-gray-100 text-gray-700">Cancel</button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const allGroups = ref([
  { id: 1, name: 'Group A' },
  { id: 2, name: 'Group B' },
])

const form = ref({
  gender: '',
  company: '',
  first_name: '',
  last_name: '',
  address: '',
  address_suffix: '',
  postal_code: '',
  location: '',
  country: 'Switzerland',
  birthday: '',
  member_id: '',
  email: '',
  email_cc: '',
  phone: '',
  groups: [],
  archived: false,
})

const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await axios.post('/dashboard/members', form.value)
    router.visit('/dashboard/members')
  } catch (e) {
    error.value = 'Failed to create member.'
  }
  loading.value = false
}

async function saveAndClose() {
  loading.value = true
  error.value = ''
  try {
    await axios.post('/dashboard/members', form.value)
    router.visit('/dashboard/members')
  } catch (e) {
    error.value = 'Failed to create member.'
  }
  loading.value = false
}

function cancel() {
  router.visit('/dashboard/members')
}
</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500;
}
</style> 