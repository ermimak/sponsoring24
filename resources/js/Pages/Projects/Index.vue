<template>
  <DashboardLayout>
    <div class="py-8">
      <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Campaigns <span class="text-gray-700 text-lg font-normal ml-2">({{ totalEntries }} total)</span></h1>
          <div class="flex gap-3">
            <button class="px-4 py-2 bg-white shadow-sm rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              Filter
            </button>
            <Link :href="$route('dashboard.projects.create')" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 shadow-sm rounded-lg text-white hover:from-purple-700 hover:to-blue-700 transition-colors duration-200 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              New Campaign
            </Link>
          </div>
        </div>
        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
          <table class="min-w-full divide-y divide-gray-100">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Campaign</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Start Date</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Participants</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Donations</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Revenue</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr v-for="project in projects" :key="project.id" class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-5 whitespace-nowrap">
                  <Link :href="$route('dashboard.projects.edit', project.id)" class="text-blue-600 font-medium hover:text-blue-800 transition-colors duration-200">
                    {{ project.name?.de || project.name?.fr || project.name?.en || 'Untitled' }}
                  </Link>
                </td>
                <td class="px-6 py-5 whitespace-nowrap">
                  <div class="font-medium">{{ formatDate(project.start) }}</div>
                  <div class="text-xs text-gray-500 mt-1">{{ formatTime(project.start) }}</div>
                </td>
                <td class="px-6 py-5 whitespace-nowrap">
                  <Link :href="$route('dashboard.projects.edit', { id: project.id, tab: 'Participant' })" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors duration-200">
                    {{ project.participants_count || 0 }} participants
                  </Link>
                </td>
                <td class="px-6 py-5 whitespace-nowrap">
                  <Link :href="$route('dashboard.projects.edit', { id: project.id, tab: 'Donations' })" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors duration-200">
                    {{ project.donations_count || 0 }} donations
                  </Link>
                </td>
                <td class="px-6 py-5 whitespace-nowrap">
                  <div class="font-medium">€{{ Number.isFinite(project.donations_sum_amount) ? project.donations_sum_amount.toFixed(2) : '0.00' }}</div>
                  <div class="text-xs text-green-600 mt-1">Paid: €{{ Number.isFinite(project.paid_donations_sum_amount) ? project.paid_donations_sum_amount.toFixed(2) : '0.00' }}</div>
                </td>
                <td class="px-6 py-5 whitespace-nowrap text-right">
                  <div class="flex justify-end gap-3 table-actions">
                    <button @click="duplicateProject(project)" class="text-gray-500 hover:text-blue-600 transition-colors duration-200 p-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                      </svg>
                    </button>
                    <button @click="deleteProject(project.id)" class="text-gray-500 hover:text-red-600 transition-colors duration-200 p-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="loading" class="p-4 text-center text-gray-500">Loading...</div>
          <div v-if="error" class="p-4 text-center text-red-500">{{ error }}</div>
          <div v-if="!loading && projects.length === 0" class="p-4 text-center text-gray-500">No entries found 😕</div>
        </div>
        <div v-if="totalPages > 1" class="mt-6 flex justify-between items-center">
          <span class="text-sm text-gray-500 font-medium">Page {{ currentPage }} of {{ totalPages }} <span class="text-gray-400">|</span> {{ totalEntries }} campaigns total</span>
          <div class="flex gap-3">
            <button 
              @click="prevPage" 
              :disabled="currentPage === 1" 
              class="flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-blue-600 disabled:opacity-50 disabled:hover:bg-white disabled:hover:text-gray-400 transition-colors duration-200"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button 
              @click="nextPage" 
              :disabled="currentPage === totalPages" 
              class="flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-blue-600 disabled:opacity-50 disabled:hover:bg-white disabled:hover:text-gray-400 transition-colors duration-200"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const { props } = usePage();
const projects = ref(props.projects?.data || []);
const currentPage = ref(props.projects?.current_page || 1);
const totalPages = ref(props.projects?.last_page || 1);
const totalEntries = ref(props.projects?.total || 0);
const loading = ref(false);
const error = ref(null);

onMounted(() => {
  // Debug projects data
  console.log('Projects data:', props.projects);
  if (props.projects && props.projects.data && props.projects.data.length > 0) {
    console.log('First project:', props.projects.data[0]);
    console.log('Donations sum amount:', props.projects.data[0].donations_sum_amount);
    console.log('Paid donations sum amount:', props.projects.data[0].paid_donations_sum_amount);
  }
});

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString();
};

const formatTime = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const deleteProject = (id) => {
  if (!confirm('Are you sure you want to delete this project?')) return;
  router.delete($route('dashboard.projects.destroy', id), {
    onStart: () => { loading.value = true; error.value = null; },
    onSuccess: () => {
      projects.value = projects.value.filter(p => p.id !== id);
      totalEntries.value--;
      loading.value = false;
    },
    onError: (err) => {
      error.value = err.message || 'Failed to delete project.';
      loading.value = false;
    },
  });
};

const duplicateProject = (project) => {
  router.post('/dashboard/projects/duplicate/' + project.id, {}, {
    onStart: () => { loading.value = true; error.value = null; },
    onSuccess: (page) => {
      projects.value.push(page.props.projects.data[projects.value.length]);
      totalEntries.value++;
      loading.value = false;
    },
    onError: (err) => {
      error.value = err.message || 'Failed to duplicate project.';
      loading.value = false;
    },
  });
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    fetchProjects();
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    fetchProjects();
  }
};

const fetchProjects = () => {
  router.get($route('dashboard.projects.index'), { page: currentPage.value }, {
    preserveState: true,
    preserveScroll: true,
    onStart: () => { loading.value = true; error.value = null; },
    onSuccess: (page) => {
      projects.value = page.props.projects.data;
      currentPage.value = page.props.projects.current_page;
      totalPages.value = page.props.projects.last_page;
      totalEntries.value = page.props.projects.total;
      loading.value = false;
    },
    onError: (err) => {
      error.value = err.message || 'Failed to load projects.';
      loading.value = false;
    },
  });
};
</script>

<style scoped>
.table-actions i {
  cursor: pointer;
}
</style>