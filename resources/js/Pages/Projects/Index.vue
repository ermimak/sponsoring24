<template>
    <DashboardLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Projects</h1>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <div></div>
                    <div class="flex gap-2">
                        <button class="rounded-full bg-gray-100 p-2 hover:bg-gray-200"><i class="fas fa-filter text-gray-500"></i></button>
                        <button @click="goToCreate" class="rounded-full bg-yellow-400 hover:bg-yellow-500 text-white text-2xl flex items-center justify-center w-10 h-10"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">project</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">start</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales volume</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="project in projects" :key="project.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a :href="`/dashboard/projects/${project.id}/edit`" class="text-purple-700 font-medium hover:underline">
                                        {{ project.name?.de || project.name?.fr || project.name?.en || 'Untitled' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>{{ formatDate(project.start) }}</div>
                                    <div class="text-xs text-gray-500">{{ formatTime(project.start) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="#" class="text-purple-600 hover:underline">0 participants</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="#" class="text-purple-600 hover:underline">0 participant donations</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>Total: <span class="font-semibold">CHF 0.00</span></div>
                                    <div class="text-xs text-gray-500">Paid: CHF 0.00</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a :href="`/dashboard/projects/${project.id}/edit`" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                        <i class="fas fa-edit">edit</i>
                                    </a>
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
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const projects = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchProjects = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await axios.get('/api/projects');
        projects.value = response.data.data || response.data;
    } catch (e) {
        error.value = 'Failed to load projects.';
    } finally {
        loading.value = false;
    }
};

const deleteProject = async (id) => {
    if (!confirm('Are you sure you want to delete this project?')) return;
    try {
        await axios.delete(`/api/projects/${id}`);
        projects.value = projects.value.filter(p => p.id !== id);
    } catch (e) {
        alert('Failed to delete project.');
    }
};

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

const goToCreate = () => {
    window.location.href = '/dashboard/projects/create';
}

onMounted(fetchProjects);
</script>

<style scoped>
.table-actions i {
  cursor: pointer;
}
</style> 