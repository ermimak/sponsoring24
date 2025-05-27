<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Roles Management</h1>
    <div class="mb-4">
      <form @submit.prevent="createRole">
        <input v-model="newRole.name"
placeholder="Role name"
class="border p-2 mr-2"
required />
        <input v-model="newRole.label" placeholder="Label (optional)" class="border p-2 mr-2" />
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Role</button>
      </form>
    </div>
    <table class="min-w-full bg-white border">
      <thead>
        <tr>
          <th class="border px-4 py-2">Name</th>
          <th class="border px-4 py-2">Label</th>
          <th class="border px-4 py-2">Permissions</th>
          <th class="border px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="role in roles" :key="role.id">
          <td class="border px-4 py-2">{{ role.name }}</td>
          <td class="border px-4 py-2">{{ role.label }}</td>
          <td class="border px-4 py-2">
            <span v-for="perm in role.permissions" :key="perm.id" class="inline-block bg-gray-200 rounded px-2 py-1 text-xs mr-1">{{ perm.name }}</span>
          </td>
          <td class="border px-4 py-2">
            <button @click="editRole(role)" class="text-blue-600 mr-2">Edit</button>
            <button @click="deleteRole(role.id)" class="text-red-600">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="editingRole" class="mt-6 p-4 border rounded bg-gray-50">
      <h2 class="font-semibold mb-2">Edit Role: {{ editingRole.name }}</h2>
      <form @submit.prevent="updateRole">
        <input v-model="editingRole.name" class="border p-2 mr-2" required />
        <input v-model="editingRole.label" class="border p-2 mr-2" />
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
        <button @click="editingRole = null" type="button" class="ml-2 px-4 py-2 rounded border">Cancel</button>
      </form>
      <div class="mt-4">
        <h3 class="font-semibold mb-1">Assign Permissions</h3>
        <div>
          <label v-for="perm in permissions" :key="perm.id" class="inline-flex items-center mr-3">
            <input type="checkbox"
:value="perm.id"
v-model="editingRolePermissionIds"
@change="togglePermission(perm.id)" />
            <span class="ml-1">{{ perm.name }}</span>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const roles = ref([])
const permissions = ref([])
const newRole = ref({ name: '', label: '' })
const editingRole = ref(null)
const editingRolePermissionIds = ref([])

async function fetchRoles() {
  const res = await axios.get('/api/roles')
  roles.value = res.data
}
async function fetchPermissions() {
  const res = await axios.get('/api/permissions')
  permissions.value = res.data
}
onMounted(() => {
  fetchRoles()
  fetchPermissions()
})
async function createRole() {
  await axios.post('/api/roles', newRole.value)
  newRole.value = { name: '', label: '' }
  fetchRoles()
}
function editRole(role) {
  editingRole.value = { ...role }
  editingRolePermissionIds.value = role.permissions.map(p => p.id)
}
async function updateRole() {
  await axios.put(`/api/roles/${editingRole.value.id}`, editingRole.value)
  // Update permissions
  for (const perm of permissions.value) {
    const has = editingRolePermissionIds.value.includes(perm.id)
    const already = editingRole.value.permissions.some(p => p.id === perm.id)
    if (has && !already) {
      await axios.post(`/api/roles/${editingRole.value.id}/assign-permission`, { permission_id: perm.id })
    } else if (!has && already) {
      await axios.post(`/api/roles/${editingRole.value.id}/remove-permission`, { permission_id: perm.id })
    }
  }
  editingRole.value = null
  fetchRoles()
}
async function deleteRole(id) {
  await axios.delete(`/api/roles/${id}`)
  fetchRoles()
}
function togglePermission(permId) {
  // handled in updateRole
}
</script> 