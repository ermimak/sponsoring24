<template>
    <AdminLayout title="News Management">
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
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-semibold text-gray-800">News Management</h1>
                            <button @click="showCreateModal = true" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Add News Item
                            </button>
                        </div>

                        <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in news" :key="item.id">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ item.title }}</div>
                                            <div class="text-sm text-gray-500 truncate max-w-md">{{ item.excerpt }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(item.published_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="item.is_published" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Published
                                            </span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Draft
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="editNewsItem(item)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                Edit
                                            </button>
                                            <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="news.length === 0">
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            No news items found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="showCreateModal || showEditModal" @close="closeModal">
            <template #title>{{ showEditModal ? 'Edit News Item' : 'Add News Item' }}</template>
            <template #content>
                <form @submit.prevent="submitForm">
                    <div class="mb-4">
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.title"
                            required
                            autofocus
                        />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>
                    
                    <div class="mb-4">
                        <InputLabel for="excerpt" value="Excerpt" />
                        <textarea
                            id="excerpt"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            v-model="form.excerpt"
                            rows="2"
                            required
                        ></textarea>
                        <InputError :message="form.errors.excerpt" class="mt-2" />
                    </div>
                    
                    <div class="mb-4">
                        <InputLabel for="content" value="Content" />
                        <textarea
                            id="content"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            v-model="form.content"
                            rows="6"
                            required
                        ></textarea>
                        <InputError :message="form.errors.content" class="mt-2" />
                    </div>
                    
                    <div class="mb-4">
                        <InputLabel for="image_url" value="Image URL" />
                        <!-- TODO: add image upload -->
                        <input type="file" @change="onFileChange($event, 'image_url')" />
                        <InputError :message="form.errors.image_url" class="mt-2" />
                    </div>
                    
                    <div class="mb-4 flex items-center">
                        <input
                            id="is_published"
                            type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.is_published"
                        />
                        <label for="is_published" class="ml-2 block text-sm text-gray-900">
                            Publish immediately
                        </label>
                    </div>
                    

                </form>
            </template>
            <template #footer>
                <SecondaryButton @click="closeModal" class="mr-3">
                    Cancel
                </SecondaryButton>
                <PrimaryButton @click="submitForm" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ showEditModal ? 'Update' : 'Create' }}
                </PrimaryButton>
            </template>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>Delete News Item</template>
            <template #content>
                <p class="mb-6 text-gray-600">Are you sure you want to delete this news item? This action cannot be undone.</p>
            </template>
            <template #footer>
                <SecondaryButton @click="showDeleteModal = false" class="mr-3">
                    Cancel
                </SecondaryButton>
                <DangerButton @click="deleteNewsItem" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Delete
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
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { route } from '@/ziggy-plugin';
import { format } from 'date-fns';

const props = defineProps({
    news: Array,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const currentItem = ref(null);

const form = useForm({
    title: '',
    excerpt: '',
    content: '',
    image_url: '',
    is_published: true,
});

const formatDate = (dateString) => {
    return format(new Date(dateString), 'PPP');
};

const onFileChange = (event, field) => {
    form[field] = event.target.files[0];
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    form.reset();
    form.clearErrors();
};

const editNewsItem = (item) => {
    currentItem.value = item;
    form.title = item.title;
    form.excerpt = item.excerpt;
    form.content = item.content;
    form.image_url = item.image_url;
    form.is_published = item.is_published;
    showEditModal.value = true;
};

const createNewsItem = async () => {
    form.post(route('admin.content.news.create'), {
        onSuccess: () => {
            closeModal();
        },
    });
};

const updateNewsItem = async () => {
    form.put(route('admin.content.news.update'), {
        onSuccess: () => {
            closeModal();
        },
    });
};

const submitForm = () => {
    if (showEditModal.value) {
        updateNewsItem();
    } else {
        createNewsItem();
    }
};

const confirmDelete = (item) => {
    currentItem.value = item;
    showDeleteModal.value = true;
};

const deleteNewsItem = () => {
    form.delete(route('admin.content.news.destroy', currentItem.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
};
</script>
