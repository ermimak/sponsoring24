<template>
    <DashboardLayout title="News Show">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                News Show
            </h2>
        </template>
    </DashboardLayout>
    <div class="p-6">
        <h1 class="text-2xl font-bold">{{ news.title }}</h1>
        <p class="mt-4">{{ news.content }}</p>
    </div>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { ref } from 'vue';

const news = ref({
    title: 'News Title',
    content: 'News Content',
});
</script>
