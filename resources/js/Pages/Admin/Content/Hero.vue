<template>
    <AdminLayout title="Hero Section Management">
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
                        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Hero Section Management</h1>
                        
                        <form @submit.prevent="updateHero">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1 md:col-span-2">
                                    <InputLabel for="headline" value="Headline" />
                                    <TextInput
                                        id="headline"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.headline"
                                        required
                                        autofocus
                                    />
                                    <InputError :message="form.errors.headline" class="mt-2" />
                                </div>
                                
                                <div class="col-span-1 md:col-span-2">
                                    <InputLabel for="subheading" value="Subheading" />
                                    <textarea
                                        id="subheading"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        v-model="form.subheading"
                                        rows="3"
                                        required
                                    ></textarea>
                                    <InputError :message="form.errors.subheading" class="mt-2" />
                                </div>
                                
                                <div>
                                    <InputLabel for="cta_primary_text" value="Primary CTA Text" />
                                    <TextInput
                                        id="cta_primary_text"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.cta_primary_text"
                                        required
                                    />
                                    <InputError :message="form.errors.cta_primary_text" class="mt-2" />
                                </div>
                                
                                <div>
                                    <InputLabel for="cta_primary_url" value="Primary CTA URL" />
                                    <TextInput
                                        id="cta_primary_url"
                                        type="url"
                                        class="mt-1 block w-full"
                                        v-model="form.cta_primary_url"
                                        required
                                    />
                                    <InputError :message="form.errors.cta_primary_url" class="mt-2" />
                                </div>
                                
                                <div>
                                    <InputLabel for="cta_secondary_text" value="Secondary CTA Text (Optional)" />
                                    <TextInput
                                        id="cta_secondary_text"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.cta_secondary_text"
                                    />
                                    <InputError :message="form.errors.cta_secondary_text" class="mt-2" />
                                </div>
                                
                                <div>
                                    <InputLabel for="cta_secondary_url" value="Secondary CTA URL (Optional)" />
                                    <TextInput
                                        id="cta_secondary_url"
                                        type="url"
                                        class="mt-1 block w-full"
                                        v-model="form.cta_secondary_url"
                                    />
                                    <InputError :message="form.errors.cta_secondary_url" class="mt-2" />
                                </div>
                                
                                <div class="col-span-1 md:col-span-2">
                                    <InputLabel for="background_image" value="Background Image URL (Optional)" />
                                    <TextInput
                                        id="background_image"
                                        type="url"
                                        class="mt-1 block w-full"
                                        v-model="form.background_image"
                                        placeholder="https://example.com/image.jpg"
                                    />
                                    <InputError :message="form.errors.background_image" class="mt-2" />
                                </div>
                                
                                <div class="col-span-1 md:col-span-2">
                                    <h3 class="text-lg font-medium text-gray-700 mb-2">Preview</h3>
                                    <div class="bg-gray-100 p-6 rounded-lg border border-gray-200">
                                        <div class="relative overflow-hidden rounded-lg bg-cover bg-no-repeat p-12 text-center"
                                            :style="{ backgroundImage: form.background_image ? `url(${form.background_image})` : 'none' }">
                                            <div class="absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-fixed"
                                                :class="{ 'bg-black bg-opacity-60': form.background_image }">
                                                <div class="flex h-full items-center justify-center">
                                                    <div class="text-white">
                                                        <h2 class="mb-4 text-4xl font-semibold">{{ form.headline || 'Your Headline Here' }}</h2>
                                                        <p class="mb-6 text-xl">{{ form.subheading || 'Your subheading text goes here' }}</p>
                                                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                                                            <button type="button" class="inline-block rounded bg-primary px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg bg-indigo-600 hover:bg-indigo-700">
                                                                {{ form.cta_primary_text || 'Primary CTA' }}
                                                            </button>
                                                            <button v-if="form.cta_secondary_text" type="button" class="inline-block rounded border-2 border-neutral-50 px-6 py-2.5 text-xs font-medium uppercase leading-tight text-neutral-50 shadow-md transition duration-150 ease-in-out hover:bg-neutral-100 hover:bg-opacity-10 hover:text-neutral-200 hover:shadow-lg focus:bg-neutral-100 focus:bg-opacity-10 focus:text-neutral-200 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-neutral-200 active:bg-opacity-10 active:text-neutral-300 active:shadow-lg">
                                                                {{ form.cta_secondary_text || 'Secondary CTA' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update Hero Section
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
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { route } from '@/ziggy-plugin';

const props = defineProps({
    heroContent: Object,
});

const form = useForm({
    headline: props.heroContent?.content?.headline || '',
    subheading: props.heroContent?.content?.subheading || '',
    cta_primary_text: props.heroContent?.content?.cta_primary_text || '',
    cta_primary_url: props.heroContent?.content?.cta_primary_url || '',
    cta_secondary_text: props.heroContent?.content?.cta_secondary_text || '',
    cta_secondary_url: props.heroContent?.content?.cta_secondary_url || '',
    background_image: props.heroContent?.content?.background_image || '',
});

const updateHero = () => {
    form.post(route('admin.content.hero.update'), {
        onSuccess: () => {
            // Success notification is handled by flash message
        },
    });
};
</script>
