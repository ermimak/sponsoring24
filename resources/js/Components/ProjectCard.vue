<template>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="aspect-w-16 aspect-h-9 bg-gray-200">
            <img 
                :src="project.image || '/images/placeholder.jpg'" 
                :alt="project.title"
                class="object-cover w-full h-full"
            >
        </div>
        <div class="p-4">
            <h3 class="text-lg font-semibold mb-2">{{ project.title }}</h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ project.description }}</p>
            
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <span class="font-medium text-gray-900">{{ project.raised }}</span> raised
                </div>
                <a 
                    :href="`/projects/${project.id}`"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                    View Details →
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    project: {
        type: Object,
        required: true
    }
});
</script> 