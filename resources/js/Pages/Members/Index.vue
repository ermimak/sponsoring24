<template>
  <DashboardLayout>
    <div>
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Members <span class="text-gray-500 text-base font-normal ml-2">{{ filteredMembers.length }} entries</span></h1>
        <div class="flex gap-3 items-center">
          <button 
            @click="redirectToGroups" 
            class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-lg border border-gray-300 flex items-center shadow-sm hover:shadow transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-200" 
            :disabled="loading"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Member groups
          </button>
          <button 
            @click="handleExport" 
            class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-lg border border-gray-300 flex items-center shadow-sm hover:shadow transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-200" 
            :disabled="exporting"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ exporting ? 'Exporting...' : 'Export' }}
          </button>
          <button 
            @click="openImportExport" 
            class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-lg border border-gray-300 flex items-center shadow-sm hover:shadow transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-200"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
            </svg>
            Import
          </button>
          <button class="rounded-lg bg-white p-2.5 hover:bg-gray-50 border border-gray-300 shadow-sm hover:shadow transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
          </button>
          <button 
            @click="redirectToCreate" 
            class="rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white flex items-center justify-center w-10 h-10 shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-300"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </button>
        </div>
      </div>
      <div class="flex flex-col md:flex-row gap-6 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm w-full md:w-1/4">
          <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Search Members
          </label>
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text"
              class="w-full rounded-lg border-gray-300 pl-10 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              placeholder="Search by name, email, ID..." 
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
        </div>
        <div class="flex-1 overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <thead>
              <tr class="bg-gradient-to-r from-purple-50 to-blue-50">
                <th class="px-6 py-3.5 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider">Member ID</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider">Groups</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider">Public Registration</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider">Email Status</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider">Archived</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider">Added</th>
                <th class="px-6 py-3.5 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="member in filteredMembers" :key="member.id" class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <button @click="handleEditMember(member)" class="text-purple-600 hover:text-purple-800 font-medium transition-colors duration-200 focus:outline-none focus:underline">{{ member.name }}</button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ member.member_id || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button @click="openGroupModal(member)" class="text-purple-600 hover:text-purple-800 transition-colors duration-200 focus:outline-none focus:underline">{{ (member.groups || []).join(', ') || 'None' }}</button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                  <span class="inline-flex items-center">
                    <svg v-if="member.public_registration" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ member.public_registration ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="{
                    'px-2.5 py-1 rounded-full text-xs font-medium inline-flex items-center': true,
                    'bg-green-100 text-green-800': member.email_status === 'verified',
                    'bg-yellow-100 text-yellow-800': member.email_status === 'pending',
                    'bg-red-100 text-red-800': member.email_status === 'invalid',
                    'bg-gray-100 text-gray-800': !member.email_status
                  }">
                    <svg v-if="member.email_status === 'verified'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else-if="member.email_status === 'pending'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else-if="member.email_status === 'invalid'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ member.email_status || 'Unknown' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="{
                    'px-2.5 py-1 rounded-full text-xs font-medium inline-flex items-center': true,
                    'bg-gray-100 text-gray-800': !member.archived,
                    'bg-red-100 text-red-800': member.archived
                  }">
                    <svg v-if="member.archived" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ member.archived ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ member.created_at ? new Date(member.created_at).toLocaleDateString() : 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button 
                    @click="handleMemberDelete(member)" 
                    class="text-red-600 hover:text-red-800 flex items-center gap-1.5 transition-colors duration-200 focus:outline-none focus:underline" 
                    :disabled="loading"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                  </button>
                </td>
              </tr>
              <tr v-if="filteredMembers.length === 0">
                <td colspan="8" class="px-6 py-10 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-gray-500 mb-1">No members found</p>
                    <p class="text-gray-400 text-sm">Try adjusting your search or add a new member</p>
                    <button 
                      @click="redirectToCreate" 
                      class="mt-4 px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg flex items-center shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-300"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      Add Member
                    </button>
                  </div>
                </td>
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
      <div v-if="loading" class="p-4 text-center">
        <div class="inline-flex items-center px-4 py-2 bg-white rounded-lg shadow-sm">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-gray-700">Loading...</span>
        </div>
      </div>
      <div v-if="error" class="p-4 text-center">
        <div class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-lg border border-red-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ error }}
        </div>
      </div>
      <div v-if="message" class="p-4 text-center">
        <div class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 rounded-lg border border-green-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          {{ message }}
        </div>
      </div>
    </div>
    <Modal :show="showCreateModal" @close="closeCreateModal">
      <template #title>
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
          </svg>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 font-semibold">Create New Member</span>
        </div>
      </template>
      <template #content>
        <form @submit.prevent="submitCreateForm" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              First Name
            </label>
            <Input 
              type="text" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              v-model="createForm.first_name" 
              placeholder="Enter first name"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Last Name
            </label>
            <Input 
              type="text" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              v-model="createForm.last_name" 
              placeholder="Enter last name"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Email
            </label>
            <Input 
              type="email" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              v-model="createForm.email" 
              placeholder="Enter email address"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
              </svg>
              Member ID
            </label>
            <Input 
              type="text" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              v-model="createForm.member_id" 
              placeholder="Enter unique member ID"
            />
          </div>
          <div class="flex justify-end pt-2">
            <button 
              type="button" 
              @click="closeCreateModal" 
              class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 shadow-sm hover:bg-gray-50 mr-3 flex items-center transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-200"
            >
              Cancel
            </button>
            <Button 
              type="submit" 
              class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg flex items-center shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-300"
              :disabled="loading"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Create Member
            </Button>
          </div>
        </form>
      </template>
    </Modal>
    <Modal :show="showEditModal" @close="closeEditModal">
      <template #title>
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
          </svg>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 font-semibold">Edit Member</span>
        </div>
      </template>
      <template #content>
        <form @submit.prevent="submitEditForm" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              First Name
            </label>
            <Input 
              type="text" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              v-model="selectedMember.first_name" 
              placeholder="Enter first name"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Last Name
            </label>
            <Input 
              type="text" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              v-model="selectedMember.last_name" 
              placeholder="Enter last name"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Email
            </label>
            <Input 
              type="email" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              v-model="selectedMember.email" 
              placeholder="Enter email address"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
              </svg>
              Member ID
            </label>
            <Input 
              type="text" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              v-model="selectedMember.member_id" 
              placeholder="Enter unique member ID"
            />
          </div>
          <div class="flex justify-end pt-2">
            <button 
              type="button" 
              @click="closeEditModal" 
              class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 shadow-sm hover:bg-gray-50 mr-3 flex items-center transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-200"
            >
              Cancel
            </button>
            <Button 
              type="submit" 
              class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg flex items-center shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-300"
              :disabled="loading"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Update Member
            </Button>
          </div>
        </form>
      </template>
    </Modal>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import ImportExportModal from './ImportExportModal.vue';
import GroupModal from './GroupModal.vue';
import { route } from '@/ziggy-plugin';
import axios from 'axios';

// Configure axios to use the same protocol as the current page
axios.defaults.baseURL = window.location.origin;
// Force HTTP for local development
if (window.location.hostname === 'localhost') {
  axios.defaults.baseURL = 'http://localhost';
}

const props = defineProps({
  members: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

const searchQuery = ref(props.filters.search || '');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showImportExport = ref(false);
const showGroupModal = ref(false);
const selectedMember = ref(null);
const editingMember = ref(null);
const editingGroup = ref(null);
const loading = ref(false);
const exporting = ref(false);
const error = ref(null);
const message = ref('');
const members = ref([...props.members]);

const createForm = ref({
  first_name: '',
  last_name: '',
  email: '',
  member_id: ''
});

onMounted(() => {
  console.log('Members/Index component mounted');
  console.log('Members count:', props.members?.length || 0);
  loading.value = false;
  
  // Initialize members from props if available
  if (props.members && props.members.length > 0) {
    members.value = props.members.map(m => ({
      ...m,
      name: `${m.first_name || ''} ${m.last_name || ''}`.trim() || 'N/A',
      member_id: m.member_id || 'N/A',
      groups: Array.isArray(m.member_groups) ? m.member_groups.map(g => g.name) : [],
      public_registration: !!m.public_registration,
      email_status: m.email_status || 'OK',
      archived: !!m.archived,
      added: m.created_at ? new Date(m.created_at).toLocaleDateString() : 'N/A',
    }));
  } else {
    fetchMembers();
  }
});

const filteredMembers = computed(() => {
  if (!members.value.length) return [];
  if (!searchQuery.value.trim()) return members.value;
  
  const query = searchQuery.value.toLowerCase().trim();
  return members.value.filter(member => {
    return (
      (member.first_name && member.first_name.toLowerCase().includes(query)) ||
      (member.last_name && member.last_name.toLowerCase().includes(query)) ||
      (member.name && member.name.toLowerCase().includes(query)) ||
      (member.email && member.email.toLowerCase().includes(query)) ||
      (member.member_id && member.member_id.toLowerCase().includes(query))
    );
  });
});

function openEditModal(member) {
  selectedMember.value = { ...member };
  showEditModal.value = true;
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
    const response = await axios.post(route('dashboard.members.import'), formData, {
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
    const response = await axios.get(route('dashboard.members.export'), {
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

// Function to redirect to groups page
function redirectToGroups() {
  router.visit(route('dashboard.members.groups'));
}

// Function to redirect to create member page
function redirectToCreate() {
  router.visit(route('dashboard.members.create'));
}

// Close modals
function closeCreateModal() {
  showCreateModal.value = false;
}

function closeEditModal() {
  showEditModal.value = false;
  selectedMember.value = null;
}
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.25rem;
  padding: 0.5rem 0.75rem;
}

.input:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.5);
}
</style>