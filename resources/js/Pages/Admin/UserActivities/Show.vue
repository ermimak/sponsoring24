<template>
    <AdminLayout title="Activity Details">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('admin.user-activities.index')" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Activities
                    </Link>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-semibold text-gray-800">Activity Details</h1>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full" :class="getActivityTypeBadgeClass(activity.activity_type)">
                                {{ formatActivityType(activity.activity_type) }}
                            </span>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">User</div>
                                            <div class="mt-1 text-sm text-gray-900">
                                                <Link :href="route('admin.users.show', activity.user.id)" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ activity.user.name }} ({{ activity.user.email }})
                                                </Link>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Description</div>
                                            <div class="mt-1 text-sm text-gray-900">{{ activity.description }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Date & Time</div>
                                            <div class="mt-1 text-sm text-gray-900">{{ formatDateTime(activity.created_at) }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Technical Details</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">IP Address</div>
                                            <div class="mt-1 text-sm text-gray-900">{{ activity.ip_address || 'N/A' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">User Agent</div>
                                            <div class="mt-1 text-sm text-gray-900 break-all">{{ activity.user_agent || 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="activity.metadata" class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Data</h3>
                            <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto">{{ JSON.stringify(activity.metadata, null, 2) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { format } from 'date-fns';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { route } from '@/ziggy-plugin';

const props = defineProps({
    activity: Object,
});

const formatDateTime = (dateTime) => {
    return format(new Date(dateTime), 'MMM d, yyyy HH:mm:ss');
};

const formatActivityType = (type) => {
    return type
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const getActivityTypeBadgeClass = (type) => {
    const classes = {
        'authentication': 'bg-blue-100 text-blue-800',
        'payment': 'bg-green-100 text-green-800',
        'profile': 'bg-yellow-100 text-yellow-800',
        'project': 'bg-purple-100 text-purple-800',
        'admin': 'bg-red-100 text-red-800',
    };
    
    return classes[type] || 'bg-gray-100 text-gray-800';
};
</script>
