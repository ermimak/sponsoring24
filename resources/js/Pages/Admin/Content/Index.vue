<template>
    <AdminLayout title="Content Management">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Content Management</h1>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Featured Projects</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage projects displayed on the front page</p>
                                </div>
                                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                                    <p class="text-sm text-gray-600 mb-4">Select which projects should be featured on the homepage.</p>
                                    <Link :href="route('admin.content.featured-projects')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Manage Featured Projects
                                    </Link>
                                </div>
                            </div>
                            
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">News & Updates</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage news items displayed on the front page</p>
                                </div>
                                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                                    <p class="text-sm text-gray-600 mb-4">Create, edit, and delete news items that appear on the homepage.</p>
                                    <Link :href="route('admin.content.news')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Manage News
                                    </Link>
                                </div>
                            </div>
                            
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Hero Section</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Customize the main hero section on the front page</p>
                                </div>
                                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                                    <p class="text-sm text-gray-600 mb-4">Edit the headline, subheading, and call-to-action buttons.</p>
                                    <Link :href="route('admin.content.hero')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Edit Hero Section
                                    </Link>
                                </div>
                            </div>
                            
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Partner Logos</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage partner logos displayed on the front page</p>
                                </div>
                                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                                    <p class="text-sm text-gray-600 mb-4">Upload, arrange, and remove partner logos.</p>
                                    <button disabled class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-400 cursor-not-allowed">
                                        Manage Partner Logos (Coming Soon)
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Statistics</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage statistics displayed on the front page</p>
                                </div>
                                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                                    <p class="text-sm text-gray-600 mb-4">Edit the statistics shown in the impact section.</p>
                                    <button disabled class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-400 cursor-not-allowed">
                                        Edit Statistics (Coming Soon)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { route } from '@/ziggy-plugin';
</script>
