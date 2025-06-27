<template>
  <div>
    <!-- Filters -->
    <div class="bg-white shadow-md rounded-xl p-6 mb-6 border border-gray-100">
      <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        Filters
      </h2>
      <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Status
          </label>
          <select 
            v-model="filters.status" 
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
          >
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Date From
          </label>
          <input 
            v-model="filters.date_from" 
            type="date" 
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Date To
          </label>
          <input 
            v-model="filters.date_to" 
            type="date" 
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Amount Min
          </label>
          <input 
            v-model="filters.amount_min"
            type="number"
            step="0.01"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Amount Max
          </label>
          <input 
            v-model="filters.amount_max"
            type="number"
            step="0.01"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
          />
        </div>
        <div class="flex items-end">
          <button 
            type="submit" 
            class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-md flex items-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Apply Filters
          </button>
        </div>
      </form>
    </div>

    <!-- Donations Table -->
    <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          Donations <span class="ml-2 text-base font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">({{ donations.length }} entries)</span>
        </h2>
        <div class="space-x-3 flex">
          <button 
            @click="openEmailModal" 
            :disabled="selectedDonations.length === 0" 
            class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-md flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Mass Email
          </button>
          <button 
            @click="generateBulkInvoice" 
            :disabled="selectedDonations.length === 0 || isGeneratingInvoice" 
            class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-md flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ isGeneratingInvoice ? 'Generating...' : 'Bulk Invoice' }}
          </button>
        </div>
      </div>

      <table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm mb-4">
        <thead class="bg-gradient-to-r from-purple-600 to-blue-600">
          <tr>
            <th class="px-6 py-3.5 text-left text-xs font-medium text-white uppercase tracking-wider">
              <input 
                type="checkbox" 
                v-model="selectAll" 
                @change="toggleSelectAll" 
                class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
              />
            </th>
            <th class="px-6 py-3.5 text-left text-xs font-medium text-white uppercase tracking-wider">Donor</th>
            <th class="px-6 py-3.5 text-left text-xs font-medium text-white uppercase tracking-wider">Participant</th>
            <th class="px-6 py-3.5 text-left text-xs font-medium text-white uppercase tracking-wider">Amount</th>
            <th class="px-6 py-3.5 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
            <th class="px-6 py-3.5 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
            <th class="px-6 py-3.5 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="donation in donations" :key="donation.id" class="hover:bg-gray-50 transition-colors duration-150">
            <td class="px-6 py-4 whitespace-nowrap">
              <input 
                type="checkbox" 
                :value="donation.id" 
                v-model="selectedDonations" 
                class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
              />
            </td>
            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ donation.donor_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ donation.participant_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ donation.amount }} {{ donation.currency }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ donation.date }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="{
                  'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200': donation.status === 'completed',
                  'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-200': donation.status === 'pending',
                }"
                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm"
              >
                {{ donation.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end space-x-3">
                <button 
                  v-if="donation.donor_email" 
                  @click="openSingleEmailModal(donation)" 
                  class="text-purple-600 hover:text-purple-900 transition-colors duration-150 p-1.5 rounded-full hover:bg-purple-50" 
                  title="Send Email"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </button>
                <a 
                  v-if="donation.participant_id"
                  :href="route('donations.preview', { donation: donation.id })"
                  target="_blank"
                  class="text-purple-600 hover:text-purple-900 transition-colors duration-150 p-1.5 rounded-full hover:bg-purple-50"
                  title="View Invoice"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </a>
              </div>
            </td>
          </tr>
          <tr v-if="donations.length === 0">
            <td colspan="7" class="px-6 py-8 text-center">
              <div class="flex flex-col items-center justify-center text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-gray-500 text-lg">No donations found</p>
                <p class="text-gray-400 text-sm">Try adjusting your filters or check back later</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mass Email Modal -->
    <div v-if="showEmailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto border border-gray-100">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Send Mass Email
          </h2>
          <button @click="closeEmailModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="sendMassEmail" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z" />
              </svg>
              Email Template
            </label>
            <select 
              v-model="emailForm.template_id" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              required 
              @change="handleTemplateChange"
            >
              <option value="">Select a template</option>
              <option v-for="template in emailTemplates" :key="template.id" :value="template.id">
                {{ template.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Subject
            </label>
            <input 
              v-model="emailForm.subject"
              type="text"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
              required 
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Message
            </label>
            <textarea 
              v-model="emailForm.body"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
              rows="6"
              required
            ></textarea>
          </div>
          <div class="flex justify-end space-x-4 pt-2">
            <button 
              @click="closeEmailModal" 
              type="button" 
              class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 font-medium shadow-sm border border-gray-200 flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Cancel
            </button>
            <button 
              type="submit" 
              :disabled="isSending" 
              class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-md flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
              {{ isSending ? 'Sending...' : 'Send Email' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Single Email Modal -->
    <div v-if="showSingleEmailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto border border-gray-100">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Email to {{ selectedDonation?.donor_name }}
          </h2>
          <button @click="closeSingleEmailModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="sendSingleEmail" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z" />
              </svg>
              Email Template
            </label>
            <select 
              v-model="singleEmailForm.template_id" 
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200" 
              required 
              @change="handleSingleTemplateChange"
            >
              <option value="">Select a template</option>
              <option v-for="template in emailTemplates" :key="template.id" :value="template.id">
                {{ template.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Subject
            </label>
            <input 
              v-model="singleEmailForm.subject"
              type="text"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
              required 
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Message
            </label>
            <textarea 
              v-model="singleEmailForm.body"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-colors duration-200"
              rows="6"
              required
            ></textarea>
          </div>
          <div class="flex justify-end space-x-4 pt-2">
            <button 
              @click="closeSingleEmailModal" 
              type="button" 
              class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 font-medium shadow-sm border border-gray-200 flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Cancel
            </button>
            <button 
              type="submit" 
              :disabled="isSending" 
              class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-md flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
              {{ isSending ? 'Sending...' : 'Send Email' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Flash Messages -->
    <div v-if="flashMessage.success" class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative mb-4 shadow-sm flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ flashMessage.success }}</span>
    </div>
    <div v-if="flashMessage.error" class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg relative mb-4 shadow-sm flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ flashMessage.error }}</span>
    </div>
    <div v-if="flashMessage.loading" class="fixed bottom-4 right-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white px-4 py-3 rounded-lg shadow-md flex items-center">
      <svg class="animate-spin mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span>{{ flashMessage.loading }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
import axios from 'axios'
import { route } from 'ziggy-js'

const props = defineProps({
  project: Object,
  filters: Object,
  donations: Array,
})

const page = usePage()
const donations = ref([]) // Initialize as empty
const filters = ref({
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  amount_min: props.filters.amount_min || '',
  amount_max: props.filters.amount_max || '',
})
const selectedDonations = ref([])
const selectAll = ref(false)
const showEmailModal = ref(false)
const showSingleEmailModal = ref(false)
const selectedDonation = ref(null)
const emailTemplates = ref([])
const isSending = ref(false)

const emailForm = ref({
  template_id: '',
  subject: '',
  body: '',
})

const singleEmailForm = ref({
  template_id: '',
  subject: '',
  body: '',
})

const isGeneratingInvoice = ref(false)

// Local flash message implementation to replace useToast
const flashMessage = ref({
  success: page.props.flash?.success || '',
  error: page.props.flash?.error || '',
  loading: ''
})

// Helper functions to show flash messages
const showSuccessMessage = (message) => {
  flashMessage.value.success = message
  setTimeout(() => {
    flashMessage.value.success = ''
  }, 5000)
}

const showErrorMessage = (message) => {
  flashMessage.value.error = message
  setTimeout(() => {
    flashMessage.value.error = ''
  }, 5000)
}

const showLoadingMessage = (message) => {
  flashMessage.value.loading = message
  setTimeout(() => {
    flashMessage.value.loading = ''
  }, 3000)
}

// Debounce utility
const debounce = (fn, delay) => {
  let timeoutId
  return (...args) => {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => fn(...args), delay)
  }
}

// Fetch email templates for the project
const fetchEmailTemplates = async () => {
  try {
    console.log('Fetching email templates for project:', props.project.id)
    const response = await axios.get('/dashboard/email-templates', {
      params: {
        project_id: props.project.id,
      },
    })
    console.log('Email templates response:', response.data)
    emailTemplates.value = response.data.data || response.data
    console.log('Email templates loaded:', emailTemplates.value.length)
  } catch (error) {
    console.error('Error fetching email templates:', error)
  }
}

// Fetch donations without navigating away from the current route
const fetchDonations = debounce(async () => {
  try {
    const response = await axios.get(route('dashboard.project.donations.fetch', props.project.id), {
      params: {
        status: filters.value.status,
        date_from: filters.value.date_from,
        date_to: filters.value.date_to,
        amount_min: filters.value.amount_min,
        amount_max: filters.value.amount_max,
      },
    })
    donations.value = response.data.data || response.data.map(donation => {
      return {
        'id': donation.id,
        'donor_name': donation.supporter_email ?? 'Anonymous',
        'amount': donation.amount,
        'currency': donation.currency,
        'date': donation.billing_date ? donation.billing_date.format('Y-m-d') : null,
        'status': donation.status,
        'participant_name': donation.participant ? (donation.participant.first_name ?? '') + ' ' + (donation.participant.last_name ?? '') : 'N/A',
        'participant_id': donation.participant_id,
      }
    })
    selectedDonations.value = [] // Reset selections after fetching
    selectAll.value = false // Reset select all checkbox
  } catch (error) {
    console.error('Failed to fetch donations:', error)
    donations.value = []
    selectedDonations.value = []
    selectAll.value = false
  }
}, 300)

function toggleSelectAll() {
  if (selectAll.value) {
    selectedDonations.value = donations.value.map(donation => donation.id)
  } else {
    selectedDonations.value = []
  }
}

  const handleTemplateChange = async () => {
    if (!emailForm.value.template_id) return
    
    try {
      const template = emailTemplates.value.find(t => t.id === emailForm.value.template_id)
      if (template) {
        emailForm.value.subject = template.subject || ''
        emailForm.value.body = template.body || ''
      }
    } catch (error) {
      console.error('Error loading template:', error)
    }
  }

  const handleSingleTemplateChange = async () => {
    if (!singleEmailForm.value.template_id) return
    
    try {
      const template = emailTemplates.value.find(t => t.id === singleEmailForm.value.template_id)
      if (template) {
        singleEmailForm.value.subject = template.subject || ''
        singleEmailForm.value.body = template.body || ''
      }
    } catch (error) {
      console.error('Error loading template:', error)
    }
  }

  const openEmailModal = async () => {
    if (selectedDonations.value.length === 0) {
      showErrorMessage('Please select at least one donation to send emails')
      return
    }
    
    // Reset form
    emailForm.value = {
      template_id: '',
      subject: '',
      body: ''
    }
    
    // Always fetch templates to ensure we have the latest
    showLoadingMessage('Loading email templates...')
    await fetchEmailTemplates()
    
    if (emailTemplates.value.length === 0) {
      showErrorMessage('No email templates found for this project. Please create at least one template.')
    }
    
    showEmailModal.value = true
  }

  const closeEmailModal = () => {
    showEmailModal.value = false
    emailForm.value = {
      template_id: '',
      subject: '',
      body: ''
    }
  }

  const openSingleEmailModal = async (donation) => {
    selectedDonation.value = donation
    
    // Reset form
    singleEmailForm.value = {
      template_id: '',
      subject: '',
      body: ''
    }
    
    // Always fetch templates to ensure we have the latest
    showLoadingMessage('Loading email templates...')
    await fetchEmailTemplates()
    
    if (emailTemplates.value.length === 0) {
      showErrorMessage('No email templates found for this project. Please create at least one template.')
    }
    
    showSingleEmailModal.value = true
  }

  const closeSingleEmailModal = () => {
    showSingleEmailModal.value = false
    selectedDonation.value = null
    singleEmailForm.value = {
      template_id: '',
      subject: '',
      body: ''
    }
  }

  const sendMassEmail = async () => {
    if (!emailForm.value.template_id) {
      showErrorMessage('Please select an email template.')
      setTimeout(() => {
        flashMessage.value.error = ''
      }, 3000)
      return
    }
    
    isSending.value = true
    try {
      const response = await axios.post(route('project.donations.massEmail', props.project.id), {
        donation_ids: selectedDonations.value,
        template_id: emailForm.value.template_id,
        subject: emailForm.value.subject,
        body: emailForm.value.body,
      })

      closeEmailModal()
      selectedDonations.value = []
      selectAll.value = false

      // Flash message
      showSuccessMessage(`Mass email sent successfully. ${response.data.results.success} sent, ${response.data.results.failed} failed, ${response.data.results.skipped} skipped.`)
      setTimeout(() => {
        flashMessage.value.success = ''
      }, 5000)
    } catch (error) {
      console.error('Error sending mass email:', error)
      showErrorMessage(error.response?.data?.error || 'Failed to send mass email.')
      setTimeout(() => {
        flashMessage.value.error = ''
      }, 5000)
    } finally {
      isSending.value = false
    }
  }
  
  const sendSingleEmail = async () => {
    if (!singleEmailForm.value.template_id || !selectedDonation.value) {
      showErrorMessage('Please select an email template.')
      setTimeout(() => {
        flashMessage.value.error = ''
      }, 3000)
      return
    }
    
    isSending.value = true
    try {
      const response = await axios.post(
        route('project.donations.sendEmail', { 
          projectId: props.project.id, 
          donationId: selectedDonation.value.id 
        }), 
        {
          template_id: singleEmailForm.value.template_id,
          subject: singleEmailForm.value.subject,
          body: singleEmailForm.value.body,
          project_id: props.project.id,
        }
      )

      closeSingleEmailModal()

      // Flash message
      showSuccessMessage('Email sent successfully.')
      setTimeout(() => {
        flashMessage.value.success = ''
      }, 3000)
    } catch (error) {
      console.error('Error sending email:', error)
      showErrorMessage(error.response?.data?.error || 'Failed to send email.')
      setTimeout(() => {
        flashMessage.value.error = ''
      }, 3000)
    } finally {
      isSending.value = false
    }
  }

  const generateBulkInvoice = async () => {
    if (selectedDonations.value.length === 0) return

    isGeneratingInvoice.value = true
    try {
      const response = await axios.post(route('project.donations.bulkInvoice', props.project.id), {
        donation_ids: selectedDonations.value,
      }, {
        responseType: 'blob', // Important for handling binary data (PDF)
      })

      // Create a URL for the PDF blob and trigger download
      const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', `bulk_invoice_${props.project.id}_${new Date().toISOString().slice(0, 10)}.pdf`)
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)

      // Reset selections and refresh data
      selectedDonations.value = []
      selectAll.value = false
      // No need to refetch, as generating invoice doesn't change donation status in this flow
      // fetchDonations()
      alert('Bulk invoice generated successfully!');

    } catch (error) {
      console.error('Failed to generate bulk invoice:', error)
      alert('Failed to generate bulk invoice. Please try again.')
    } finally {
      isGeneratingInvoice.value = false
    }
  }

onMounted(() => {
  // Always fetch data on mount based on initial filters from props or default
  fetchDonations();
  fetchEmailTemplates();
})

// Watch project changes, but avoid infinite loops
watch(() => props.project.id, (newProjectId) => {
  if (newProjectId) {
    fetchDonations()
  }
})

// Apply filters function that's missing
const applyFilters = () => {
  fetchDonations();
}

// Watch filters, but only fetch if values actually change (deep watch)
watch(filters, (newFilters, oldFilters) => {
  // Deep compare to avoid unnecessary fetches
  if (JSON.stringify(newFilters) !== JSON.stringify(oldFilters)) {
    fetchDonations();
  }
}, { deep: true });

// Watch for changes in the initial donations prop if it's provided later (less common for initial load)
// watch(() => props.donations, (newDonations) => {
//   if (newDonations && newDonations.length > 0) {
//     donations.value = newDonations;
//   }
// }, { immediate: true }); // Use immediate to potentially load initial prop data

</script>

<style scoped>
.input {
  @apply border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 w-full;
}
</style>