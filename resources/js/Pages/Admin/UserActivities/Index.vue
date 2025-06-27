<template>
    <AdminLayout title="User Activities">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-semibold text-gray-800">User Activities</h1>
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <h2 class="text-lg font-medium text-gray-700 mb-3">Filters</h2>
                            <form @submit.prevent="applyFilters">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <InputLabel for="activity_type" value="Activity Type" />
                                        <select
                                            id="activity_type"
                                            v-model="filters.activity_type"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                        >
                                            <option value="">All Types</option>
                                            <option v-for="type in activityTypes" :key="type" :value="type">
                                                {{ formatActivityType(type) }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <InputLabel for="date_from" value="From Date" />
                                        <TextInput
                                            id="date_from"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="filters.date_from"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel for="date_to" value="To Date" />
                                        <TextInput
                                            id="date_to"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="filters.date_to"
                                        />
                                    </div>
                                    <div class="flex items-end">
                                        <PrimaryButton type="submit" class="mr-2">
                                            Apply Filters
                                        </PrimaryButton>
                                        <SecondaryButton type="button" @click="resetFilters">
                                            Reset
                                        </SecondaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Activities Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Activity Type
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            IP Address
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date/Time
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="activities.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No activities found
                                        </td>
                                    </tr>
                                    <tr v-for="activity in activities.data" :key="activity.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <Link :href="route('admin.users.show', activity.user.id)" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ activity.user.name }}
                                                    </Link>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ activity.user.email }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getActivityTypeBadgeClass(activity.activity_type)">
                                                {{ formatActivityType(activity.activity_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate">
                                                {{ activity.description }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ activity.ip_address || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDateTime(activity.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('admin.user-activities.show', activity.id)" class="text-indigo-600 hover:text-indigo-900">
                                                View Details
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="activities.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { route } from '@/ziggy-plugin';
import { format } from 'date-fns';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    activities: Object,
    activityTypes: Array,
    filters: Object,
});

const filters = ref({
    activity_type: props.filters.activity_type || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const applyFilters = () => {
    router.get(route('admin.user-activities.index'), {
        activity_type: filters.value.activity_type,
        date_from: filters.value.date_from,
        date_to: filters.value.date_to,
    }, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    filters.value = {
        activity_type: '',
        date_from: '',
        date_to: '',
    };
    applyFilters();
};

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
