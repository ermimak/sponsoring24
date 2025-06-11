<template>
    <AdminLayout title="Featured Projects Management">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('admin.content.index')" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Content Management
                    </Link>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Featured Projects Management</h1>
                        
                        <p class="mb-4 text-gray-600">
                            Select up to 6 projects to feature on the front page. These projects will be displayed in the "Featured Projects" section.
                        </p>
                        
                        <form @submit.prevent="updateFeaturedProjects">
                            <div class="mb-6">
                                <h2 class="text-lg font-medium text-gray-700 mb-2">Currently Featured Projects</h2>
                                <div v-if="featuredProjects.length === 0" class="text-gray-500 italic">
                                    No projects are currently featured.
                                </div>
                                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div v-for="project in featuredProjects" :key="project.id" class="border rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 mr-3">
                                                <img v-if="project.image_square" :src="project.image_square" alt="Project thumbnail" class="w-16 h-16 object-cover rounded">
                                                <div v-else class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                                    <span class="text-gray-400">No image</span>
                                                </div>
                                            </div>
                                            <div class="flex-grow">
                                                <h3 class="font-medium">{{ project.title || project.name['de'] || 'Unnamed Project' }}</h3>
                                                <p class="text-sm text-gray-500 truncate">{{ project.description['de'] || project.short_description['de'] || 'No description available' }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex justify-end">
                                            <button 
                                                type="button" 
                                                @click="removeFeatured(project.id)" 
                                                class="text-red-600 hover:text-red-800 text-sm"
                                            >
                                                Remove from featured
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h2 class="text-lg font-medium text-gray-700 mb-2">Available Projects</h2>
                                <div v-if="availableProjects.length === 0" class="text-gray-500 italic">
                                    No additional projects available to feature.
                                </div>
                                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div v-for="project in availableProjects" :key="project.id" class="border rounded-lg p-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 mr-3">
                                                <img v-if="project.image_square" :src="project.image_square" alt="Project thumbnail" class="w-16 h-16 object-cover rounded">
                                                <div v-else class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                                    <span class="text-gray-400">No image</span>
                                                </div>
                                            </div>
                                            <div class="flex-grow">
                                                <h3 class="font-medium">{{ project.title || project.name['de'] || 'Unnamed Project' }}</h3>
                                                <p class="text-sm text-gray-500 truncate">{{ project.description['de'] || project.short_description['de'] || 'No description available' }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex justify-end">
                                            <button 
                                                type="button" 
                                                @click="addFeatured(project.id)" 
                                                class="text-indigo-600 hover:text-indigo-800 text-sm"
                                                :disabled="selectedProjects.length >= 6"
                                            >
                                                Add to featured
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Save Changes
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { route } from '@/ziggy-plugin';

const props = defineProps({
    featuredProjects: Array,
    availableProjects: Array,
});

const selectedProjects = ref(props.featuredProjects.map(project => project.id));

const form = useForm({
    featured_project_ids: selectedProjects.value,
});

const addFeatured = (projectId) => {
    if (selectedProjects.value.length < 6 && !selectedProjects.value.includes(projectId)) {
        selectedProjects.value.push(projectId);
        form.featured_project_ids = selectedProjects.value;
    }
};

const removeFeatured = (projectId) => {
    const index = selectedProjects.value.indexOf(projectId);
    if (index !== -1) {
        selectedProjects.value.splice(index, 1);
        form.featured_project_ids = selectedProjects.value;
    }
};

const updateFeaturedProjects = () => {
    form.post(route('admin.content.featured-projects.update'), {
        onSuccess: () => {
            // Success notification is handled by flash message
        },
    });
};
</script>
