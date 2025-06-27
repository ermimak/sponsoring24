<template>
    <AdminLayout title="Super Admin Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Super Admin Dashboard</h1>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-purple-50 p-6 rounded-lg shadow">
                                <h3 class="text-lg font-semibold text-purple-800 mb-2">Pending Approvals</h3>
                                <div class="text-3xl font-bold text-purple-600">{{ pendingUsers }}</div>
                                <div class="mt-4">
                                    <Link :href="route('admin.users.pending')" class="text-purple-700 hover:text-purple-900 font-medium">
                                        View all pending users →
                                    </Link>
                                </div>
                            </div>
                            
                            <div class="bg-emerald-50 p-6 rounded-lg shadow">
                                <h3 class="text-lg font-semibold text-emerald-800 mb-2">Total Users</h3>
                                <div class="text-3xl font-bold text-emerald-600">{{ totalUsers }}</div>
                                <div class="mt-4">
                                    <Link :href="route('admin.users.index')" class="text-emerald-700 hover:text-emerald-900 font-medium">
                                        Manage all users →
                                    </Link>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-6 rounded-lg shadow">
                                <h3 class="text-lg font-semibold text-blue-800 mb-2">Content Management</h3>
                                <div class="text-3xl font-bold text-blue-600">{{ contentItems }}</div>
                                <div class="mt-4">
                                    <Link :href="route('admin.content.index')" class="text-blue-700 hover:text-blue-900 font-medium">
                                        Manage content →
                                    </Link>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-xl font-semibold text-gray-800">Recent Registrations</h2>
                            </div>
                            <div class="p-6">
                                <div v-if="recentUsers.length" class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                                                <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="user in recentUsers" :key="user.id">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span v-if="user.approval_status === 'pending'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                    <span v-else-if="user.approval_status === 'approved'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <Link v-if="user.approval_status === 'pending'" :href="route('admin.users.show', user.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                        Review
                                                    </Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else class="text-center py-4 text-gray-500">
                                    No recent registrations
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-xl font-semibold text-gray-800">Front Page Content Management</h2>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-3">Featured Projects</h3>
                                        <p class="text-gray-600 mb-4">Manage projects displayed on the front page</p>
                                        <Link :href="route('admin.content.featured-projects')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Manage Featured Projects
                                        </Link>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-3">News & Updates</h3>
                                        <p class="text-gray-600 mb-4">Manage news items displayed on the front page</p>
                                        <Link :href="route('admin.content.news')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Manage News
                                        </Link>
                                    </div>
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
import { formatDistanceToNow } from 'date-fns';
import { route } from '@/ziggy-plugin';

const props = defineProps({
    pendingUsers: Number,
    totalUsers: Number,
    contentItems: Number,
    recentUsers: Array,
});

const formatDate = (dateString) => {
    return formatDistanceToNow(new Date(dateString), { addSuffix: true });
};
</script>
