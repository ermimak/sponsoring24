<template>
    <AdminLayout title="User Details">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('admin.users.index')" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Users
                    </Link>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-semibold text-gray-800">User Details</h1>
                            <div v-if="user.approval_status === 'pending'" class="flex space-x-3">
                                <button @click="showApproveModal = true" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Approve
                                </button>
                                <button @click="showRejectModal = true" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Reject
                                </button>
                            </div>
                            <div v-else-if="user.approval_status === 'approved'" class="px-4 py-2 bg-green-100 text-green-800 rounded-md">
                                Approved
                            </div>
                            <div v-else class="px-4 py-2 bg-red-100 text-red-800 rounded-md">
                                Rejected
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Name</div>
                                            <div class="mt-1 text-sm text-gray-900">{{ user.name }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Email</div>
                                            <div class="mt-1 text-sm text-gray-900">{{ user.email }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Registration Date</div>
                                            <div class="mt-1 text-sm text-gray-900">{{ formatDate(user.created_at) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Referral Code</div>
                                            <div class="mt-1 text-sm text-gray-900">{{ user.referral_code || 'None' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Roles & Permissions</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Roles</div>
                                            <div class="mt-1 flex flex-wrap gap-2">
                                                <span v-for="role in user.roles" :key="role" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ role }}
                                                </span>
                                                <span v-if="user.roles.length === 0" class="text-sm text-gray-500">No roles assigned</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Permissions</div>
                                            <div class="mt-1 flex flex-wrap gap-2">
                                                <span v-for="permission in user.permissions" :key="permission" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    {{ permission }}
                                                </span>
                                                <span v-if="user.permissions.length === 0" class="text-sm text-gray-500">No direct permissions</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="user.approval_status === 'rejected'" class="bg-red-50 p-6 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-red-800 mb-2">Rejection Reason</h3>
                            <p class="text-red-700">{{ user.rejection_reason }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <Modal :show="showApproveModal" @close="showApproveModal = false">
            <template #title>Approve User</template>
            <template #content>
                <p class="mb-6 text-gray-600">Are you sure you want to approve this user? They will receive an email notification and gain access to the system.</p>
            </template>
            <template #footer>
                <SecondaryButton @click="showApproveModal = false" class="mr-3">
                    Cancel
                </SecondaryButton>
                <PrimaryButton @click="approveUser" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Approve User
                </PrimaryButton>
            </template>
        </Modal>

        <!-- Reject Modal -->
        <Modal :show="showRejectModal" @close="showRejectModal = false">
            <template #title>Reject User</template>
            <template #content>
                <p class="mb-4 text-gray-600">Please provide a reason for rejecting this user. This will be included in the email notification sent to them.</p>
                
                <div class="mb-6">
                    <InputLabel for="rejection_reason" value="Rejection Reason" />
                    <textarea
                        id="rejection_reason"
                        v-model="form.rejection_reason"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="4"
                    ></textarea>
                    <InputError :message="form.errors.rejection_reason" class="mt-2" />
                </div>
            </template>
            <template #footer>
                <SecondaryButton @click="showRejectModal = false" class="mr-3">
                    Cancel
                </SecondaryButton>
                <DangerButton @click="rejectUser" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Reject User
                </DangerButton>
            </template>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { route } from '@/ziggy-plugin';
import { format } from 'date-fns';

const props = defineProps({
    user: Object,
});

const showApproveModal = ref(false);
const showRejectModal = ref(false);

const form = useForm({
    rejection_reason: '',
});

const formatDate = (dateString) => {
    return format(new Date(dateString), 'PPP');
};

const approveUser = () => {
    form.post(route('admin.users.approve', props.user.id), {
        onSuccess: () => {
            showApproveModal.value = false;
        },
    });
};

const rejectUser = () => {
    form.post(route('admin.users.reject', props.user.id), {
        onSuccess: () => {
            showRejectModal.value = false;
        },
    });
};
</script>
