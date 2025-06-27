<template>
  <AdminLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $t('License Management') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
            <div class="text-sm text-gray-500 mb-1">{{ $t('Total Licenses') }}</div>
            <div class="text-2xl font-bold">{{ stats.total }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
            <div class="text-sm text-gray-500 mb-1">{{ $t('Active Licenses') }}</div>
            <div class="text-2xl font-bold text-green-600">{{ stats.active }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
            <div class="text-sm text-gray-500 mb-1">{{ $t('Total Revenue') }}</div>
            <div class="text-2xl font-bold">CHF {{ stats.revenue?.toFixed(2) || '0.00' }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
            <div class="text-sm text-gray-500 mb-1">{{ $t('Discounts Applied') }}</div>
            <div class="text-2xl font-bold">{{ stats.discounts_applied }} (CHF {{ stats.discount_total?.toFixed(2) || '0.00' }})</div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Filters -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
              <div class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
                <div>
                  <select 
                    v-model="filters.status" 
                    @change="applyFilters"
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  >
                    <option value="">{{ $t('All Statuses') }}</option>
                    <option value="active">{{ $t('Active') }}</option>
                    <option value="expired">{{ $t('Expired') }}</option>
                    <option value="revoked">{{ $t('Revoked') }}</option>
                  </select>
                </div>
                <div class="flex">
                  <input 
                    type="text" 
                    v-model="filters.search" 
                    @keyup.enter="applyFilters"
                    placeholder="Search by key, user, email..." 
                    class="rounded-l-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  />
                  <button 
                    @click="applyFilters" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700"
                  >
                    {{ $t('Search') }}
                  </button>
                </div>
              </div>
              <div class="mt-3 md:mt-0">
                <button 
                  @click="resetFilters" 
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                >
                  {{ $t('Reset Filters') }}
                </button>
              </div>
            </div>

            <!-- Error Message -->
            <div v-if="error" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
              <p>{{ error }}</p>
            </div>

            <!-- Licenses Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('User') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('License Key') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Status') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Type') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Issued') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Expires') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Amount') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Actions') }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="licenses.data && licenses.data.length === 0">
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                      {{ $t('No licenses found.') }}
                    </td>
                  </tr>
                  <tr v-for="license in licenses.data" :key="license.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div>
                          <div class="text-sm font-medium text-gray-900">
                            {{ license.user?.name || 'Unknown User' }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ license.user?.email || 'No email' }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 font-mono">{{ license.license_key }}</div>
                      <div class="text-xs text-gray-500">{{ license.payment_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="{
                        'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                        'bg-green-100 text-green-800': license.is_active,
                        'bg-red-100 text-red-800': license.is_expired || license.status === 'revoked',
                        'bg-yellow-100 text-yellow-800': !license.is_active && !license.is_expired && license.status !== 'revoked'
                      }">
                        {{ license.status.charAt(0).toUpperCase() + license.status.slice(1) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ license.type }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(license.issued_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ formatDate(license.expires_at) }}</div>
                      <div class="text-xs text-gray-500" v-if="license.days_remaining !== null">
                        {{ license.days_remaining > 0 ? `${license.days_remaining} days left` : 'Expired' }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ license.currency }} {{ license.amount.toFixed(2) }}
                      <div v-if="license.discount_applied" class="text-xs text-green-600">
                        -{{ license.currency }} {{ license.discount_amount.toFixed(2) }} discount
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <Link :href="route('admin.licenses.show', license.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                        {{ $t('View') }}
                      </Link>
                      <button 
                        v-if="license.status !== 'revoked'" 
                        @click="confirmRevokeDialog(license)"
                        class="text-red-600 hover:text-red-900"
                      >
                        {{ $t('Revoke') }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6" v-if="licenses.links && licenses.links.length > 3">
              <div class="flex justify-between items-center">
                <div class="text-sm text-gray-700">
                  {{ $t('Showing') }} {{ licenses.from }} {{ $t('to') }} {{ licenses.to }} {{ $t('of') }} {{ licenses.total }} {{ $t('results') }}
                </div>
                <div class="flex space-x-1">
                  <Link 
                    v-for="(link, i) in licenses.links" 
                    :key="i"
                    :href="link.url"
                    :class="{
                      'px-4 py-2 text-sm rounded-md': true,
                      'bg-blue-600 text-white': link.active,
                      'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                      'opacity-50 cursor-not-allowed': !link.url,
                      'hidden': link.label === '&laquo; Previous' || link.label === 'Next &raquo;'
                    }"
                    v-html="link.label"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Revoke License Confirmation Modal -->
    <ConfirmationModal :show="showRevokeModal" @close="showRevokeModal = false">
      <template #title>
        {{ $t('Revoke License') }}
      </template>
      <template #content>
        <p>{{ $t('Are you sure you want to revoke this license? This action cannot be undone.') }}</p>
        <p class="mt-2 text-sm text-gray-500">{{ $t('License Key') }}: {{ selectedLicense?.license_key }}</p>
        <p class="text-sm text-gray-500">{{ $t('User') }}: {{ selectedLicense?.user?.name }}</p>
      </template>
      <template #footer>
        <SecondaryButton @click="showRevokeModal = false">
          {{ $t('Cancel') }}
        </SecondaryButton>
        <DangerButton class="ml-3" @click="revokeLicense">
          {{ $t('Revoke License') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
  licenses: Object,
  stats: Object,
  filters: Object,
  error: String
});

const showRevokeModal = ref(false);
const selectedLicense = ref(null);

const filters = ref({
  status: props.filters?.status || '',
  search: props.filters?.search || '',
});

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(date);
};

const applyFilters = () => {
  window.location = route('admin.licenses.index', filters.value);
};

const resetFilters = () => {
  filters.value = {
    status: '',
    search: '',
  };
  applyFilters();
};

const confirmRevokeDialog = (license) => {
  selectedLicense.value = license;
  showRevokeModal.value = true;
};

const revokeLicense = () => {
  if (!selectedLicense.value) return;
  
  const form = useForm({
    status: 'revoked',
  });
  
  form.post(route('admin.licenses.update-status', selectedLicense.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showRevokeModal.value = false;
      selectedLicense.value = null;
    },
  });
};
</script>
