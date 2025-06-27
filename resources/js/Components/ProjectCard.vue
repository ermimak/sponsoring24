<template>
    <div class="bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col h-full border border-gray-100 group transform hover:-translate-y-2">
        <div class="relative pb-[60%] overflow-hidden">
            <img :src="project.image || `/images/projects/${project.id}.jpg`" :alt="project.title" class="absolute inset-0 w-full h-full object-cover transition-all duration-500 group-hover:scale-105" />
            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-emerald-600 font-bold py-1 px-3 rounded-full text-sm shadow-md">
                {{ Math.round((project.donations_sum_amount || 0) / (project.goal || 1) * 100) }}% Funded
            </div>
        </div>
        <div class="p-8 flex flex-col flex-grow">
            <h3 class="text-xl font-bold mb-3 line-clamp-2 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-purple-600 group-hover:to-blue-600 transition-all duration-300">{{ project.title }}</h3>
            <p class="text-gray-600 mb-6 line-clamp-3 flex-grow">{{ project.description }}</p>
            <div class="mt-auto">
                <div class="w-full bg-gray-100 rounded-full h-3 mb-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-blue-500 h-3 rounded-full" :style="`width: ${Math.min(Math.round((project.donations_sum_amount || 0) / (project.goal || 1) * 100), 100)}%`"></div>
                </div>
                <div class="flex justify-between text-sm mb-6">
                    <span class="font-bold text-gray-800 text-lg">CHF {{ (project.donations_sum_amount || 0).toLocaleString() }}</span>
                    <span class="text-gray-500">of CHF {{ (project.goal || 0).toLocaleString() }}</span>
                </div>
                <a :href="`/projects/${project.id}`" class="block text-center bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-full transition-all duration-300 transform group-hover:scale-105 shadow-md">View Campaign</a>
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