<template>
  <DashboardLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Projects ({{ totalEntries }} entries)</h1>
        </div>
        <div class="flex justify-between items-center mb-4">
          <div></div>
          <div class="flex gap-2">
            <button class="rounded-full bg-gray-100 p-2 hover:bg-gray-200"><i class="fas fa-filter text-gray-500"></i></button>
            <Link :href="$route('dashboard.projects.create')" class="rounded-full bg-yellow-400 hover:bg-yellow-500 text-white text-2xl flex items-center justify-center w-10 h-10"><i class="fas fa-plus"></i></Link>
          </div>
        </div>
        <div class="bg-white shadow rounded-lg overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donate</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Volume</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="project in projects" :key="project.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link :href="$route('dashboard.projects.edit', project.id)" class="text-purple-700 font-medium hover:underline">
                    {{ project.name?.de || project.name?.fr || project.name?.en || 'Untitled' }}
                  </Link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>{{ formatDate(project.start) }}</div>
                  <div class="text-xs text-gray-500">{{ formatTime(project.start) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link :href="$route('dashboard.projects.edit', { id: project.id, tab: 'Participant' })" class="text-purple-600 hover:underline">
                    {{ project.participants_count || 0 }} participants
                  </Link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link :href="$route('dashboard.projects.edit', { id: project.id, tab: 'Donations' })" class="text-purple-600 hover:underline">
                    {{ project.donations_count || 0 }} participant donations
                  </Link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>Total: <span class="font-semibold">CHF {{ project.donations_sum_amount ? project.donations_sum_amount.toFixed(2) : '0.00' }}</span></div>
                  <div class="text-xs text-gray-500">Paid: CHF {{ project.donations_sum_amount ? project.donations_sum_amount.toFixed(2) : '0.00' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button @click="duplicateProject(project)" class="text-gray-600 hover:text-gray-800 mr-2">
                    <i class="fas fa-copy">duplicate</i>
                  </button>
                  <Link :href="$route('dashboard.projects.edit', project.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">
                    <i class="fas fa-edit">edit</i>
                  </Link>
                  <button @click="deleteProject(project.id)" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash">delete</i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="loading" class="p-4 text-center text-gray-500">Loading...</div>
          <div v-if="error" class="p-4 text-center text-red-500">{{ error }}</div>
          <div v-if="!loading && projects.length === 0" class="p-4 text-center text-gray-500">No entries found 😕</div>
        </div>
        <div v-if="totalPages > 1" class="mt-4 flex justify-between items-center">
          <span class="text-sm text-gray-500">Page {{ currentPage }} of {{ totalPages }} | {{ totalEntries }} entries total</span>
          <div class="flex gap-2">
            <button @click="prevPage" :disabled="currentPage === 1" class="rounded-full bg-gray-200 p-2 hover:bg-gray-300 disabled:opacity-50"><i class="fas fa-chevron-left"></i></button>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="rounded-full bg-gray-200 p-2 hover:bg-gray-300 disabled:opacity-50"><i class="fas fa-chevron-right"></i></button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const { props } = usePage();
const projects = ref(props.projects?.data || []);
const currentPage = ref(props.projects?.current_page || 1);
const totalPages = ref(props.projects?.last_page || 1);
const totalEntries = ref(props.projects?.total || 0);
const loading = ref(false);
const error = ref(null);

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