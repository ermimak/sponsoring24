<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Permissions Management</h1>
    <div class="mb-4">
      <form @submit.prevent="createPermission">
        <input v-model="newPermission.name" placeholder="Permission name" class="border p-2 mr-2" required />
        <input v-model="newPermission.label" placeholder="Label (optional)" class="border p-2 mr-2" />
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Permission</button>
      </form>
    </div>
    <table class="min-w-full bg-white border">
      <thead>
        <tr>
          <th class="border px-4 py-2">Name</th>
          <th class="border px-4 py-2">Label</th>
          <th class="border px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="perm in permissions" :key="perm.id">
          <td class="border px-4 py-2">{{ perm.name }}</td>
          <td class="border px-4 py-2">{{ perm.label }}</td>
          <td class="border px-4 py-2">
            <button @click="editPermission(perm)" class="text-blue-600 mr-2">Edit</button>
            <button @click="deletePermission(perm.id)" class="text-red-600">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="editingPermission" class="mt-6 p-4 border rounded bg-gray-50">
      <h2 class="font-semibold mb-2">Edit Permission: {{ editingPermission.name }}</h2>
      <form @submit.prevent="updatePermission">
        <input v-model="editingPermission.name" class="border p-2 mr-2" required />
        <input v-model="editingPermission.label" class="border p-2 mr-2" />
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
        <button @click="editingPermission = null" type="button" class="ml-2 px-4 py-2 rounded border">Cancel</button>
      </form>
      <div class="mt-4">
        <h3 class="font-semibold mb-1">Assign to Roles</h3>
        <div>
          <label v-for="role in roles" :key="role.id" class="inline-flex items-center mr-3">
            <input type="checkbox" :value="role.id" v-model="editingPermissionRoleIds" @change="toggleRole(role.id)" />
            <span class="ml-1">{{ role.name }}</span>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const permissions = ref([])
const roles = ref([])
const newPermission = ref({ name: '', label: '' })
const editingPermission = ref(null)
const editingPermissionRoleIds = ref([])

async function fetchPermissions() {
  const res = await axios.get('/api/permissions')
  permissions.value = res.data
}
async function fetchRoles() {
  const res = await axios.get('/api/roles')
  roles.value = res.data
}
onMounted(() => {
  fetchPermissions()
  fetchRoles()
})
async function createPermission() {
  await axios.post('/api/permissions', newPermission.value)
  newPermission.value = { name: '', label: '' }
  fetchPermissions()
}
function editPermission(perm) {
  editingPermission.value = { ...perm }
  editingPermissionRoleIds.value = (perm.roles || []).map(r => r.id)
}
async function updatePermission() {
  await axios.put(`/api/permissions/${editingPermission.value.id}`, editingPermission.value)
  // Update roles
  for (const role of roles.value) {
    const has = editingPermissionRoleIds.value.includes(role.id)
    const already = (editingPermission.value.roles || []).some(r => r.id === role.id)
    if (has && !already) {
      await axios.post(`/api/permissions/${editingPermission.value.id}/assign-role`, { role_id: role.id })
    } else if (!has && already) {
      await axios.post(`/api/permissions/${editingPermission.value.id}/remove-role`, { role_id: role.id })
    }
  }
  editingPermission.value = null
  fetchPermissions()
}
async function deletePermission(id) {
  await axios.delete(`/api/permissions/${id}`)
  fetchPermissions()
}
function toggleRole(roleId) {
  // handled in updatePermission
}
</script> 