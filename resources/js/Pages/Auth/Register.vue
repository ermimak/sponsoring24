<template>
    <AuthLayout title="New registration">
        <div class="bg-white p-8 md:p-10 rounded-xl shadow-2xl border border-gray-200 w-full max-w-xl">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-indigo-700 flex items-center justify-center">
                    <svg class="h-8 w-8 mr-2 text-indigo-600" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                    </svg>
                    New registration
                </h1>
                <p class="text-gray-600 mt-2">Please fill out the form below to register as an organizer and use Sponsoring24 for your fundraising efforts.</p>
                <p class="text-gray-600 mt-2">Once we have verified your information, we will send you a link to activate your account.</p>
                
                <div v-if="hasReferralCode" class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-left">
                        <p class="text-green-800 font-medium">Referral Discount Applied!</p>
                        <p class="text-green-700 text-sm">You'll receive a discount on your first annual license purchase.</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Always include the referral code field, even if empty -->
                <input type="hidden" v-model="form.referral_code" />
                <!-- Debug info for referral code (remove in production) -->
                <div v-if="hasReferralCode" class="mb-4 text-xs bg-blue-50 p-2 rounded">
                    <p>Debug: Using referral code: {{ referralCode }}</p>
                </div>
                
                <div>
                    <label for="contact_title" class="block text-sm font-medium text-gray-700 mb-1.5">Contact person Title*</label>
                    <select 
                        id="contact_title"
                        v-model="form.contact_title" 
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                    >
                        <option value="Mister">Mister</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                    </select>
                    <div v-if="form.errors.contact_title" class="mt-1.5 text-xs text-red-600 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ form.errors.contact_title }}
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="contact_first_name" class="block text-sm font-medium text-gray-700 mb-1.5">Your first name*</label>
                        <input
                            id="contact_first_name"
                            v-model="form.contact_first_name"
                            type="text"
                            required
                            placeholder="First name"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                        />
                        <div v-if="form.errors.contact_first_name" class="mt-1.5 text-xs text-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.contact_first_name }}
                        </div>
                    </div>
                    
                    <div>
                        <label for="contact_last_name" class="block text-sm font-medium text-gray-700 mb-1.5">Your last name*</label>
                        <input
                            id="contact_last_name"
                            v-model="form.contact_last_name"
                            type="text"
                            required
                            placeholder="Last name"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                        />
                        <div v-if="form.errors.contact_last_name" class="mt-1.5 text-xs text-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.contact_last_name }}
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-1.5">Name of the organization*</label>
                    <input
                        id="organization_name"
                        v-model="form.organization_name"
                        type="text"
                        required
                        placeholder="Organization name"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                    />
                    <div v-if="form.errors.organization_name" class="mt-1.5 text-xs text-red-600 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ form.errors.organization_name }}
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Address*</label>
                    <input
                        id="address"
                        v-model="form.address"
                        type="text"
                        required
                        placeholder="Street address"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                    />
                    <div v-if="form.errors.address" class="mt-1.5 text-xs text-red-600 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ form.errors.address }}
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1.5">Postal code*</label>
                        <input
                            id="postal_code"
                            v-model="form.postal_code"
                            type="text"
                            required
                            placeholder="Postal code"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                        />
                        <div v-if="form.errors.postal_code" class="mt-1.5 text-xs text-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.postal_code }}
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1.5">Location*</label>
                        <input
                            id="location"
                            v-model="form.location"
                            type="text"
                            required
                            placeholder="City/Town"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                        />
                        <div v-if="form.errors.location" class="mt-1.5 text-xs text-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.location }}
                        </div>
                    </div>
                    
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1.5">Land*</label>
                        <select
                            id="country"
                            v-model="form.country"
                            required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                        >
                            <option value="Switzerland">Switzerland</option>
                            <option value="Germany">Germany</option>
                            <option value="Austria">Austria</option>
                            <option value="France">France</option>
                            <option value="Italy">Italy</option>
                        </select>
                        <div v-if="form.errors.country" class="mt-1.5 text-xs text-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.country }}
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">E-mail*</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            placeholder="your.email@example.com"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                        />
                        <div v-if="form.errors.email" class="mt-1.5 text-xs text-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.email }}
                        </div>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Telephone*</label>
                        <input
                            id="phone"
                            v-model="form.phone"
                            type="tel"
                            required
                            placeholder="+41 XX XXX XX XX"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                        />
                        <div v-if="form.errors.phone" class="mt-1.5 text-xs text-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.phone }}
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-6 mt-2">
                    <h3 class="font-semibold text-lg text-gray-800 mb-3">Account credentials</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password*</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                placeholder="Choose a secure password"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                            />
                            <div v-if="form.errors.password" class="mt-1.5 text-xs text-red-600 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password*</label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                placeholder="Confirm your password"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                            />
                            <div v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-red-600 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ form.errors.password_confirmation }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-6 mt-2">
                    <h3 class="font-semibold text-lg text-gray-800 mb-3">Terms and conditions</h3>
                    
                    <div class="flex items-center space-x-2 mt-2">
                        <input
                            id="terms"
                            v-model="form.terms"
                            type="checkbox"
                            required
                            class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <label for="terms" class="text-sm text-gray-700">
                            I agree to the <a href="#" class="text-purple-600 hover:text-purple-800 font-medium">Terms of Service</a> and <a href="#" class="text-purple-600 hover:text-purple-800 font-medium">Privacy Policy</a>
                        </label>
                    </div>
                    <div v-if="form.errors.terms" class="mt-1 text-xs text-red-600 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ form.errors.terms }}
                    </div>
                    
                    <div class="flex items-center space-x-2 mt-4">
                        <input
                            id="newsletter"
                            v-model="form.newsletter"
                            type="checkbox"
                            class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        />
                        <label for="newsletter" class="text-sm text-gray-700">
                            I would like to receive the newsletter
                        </label>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-3 px-6 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors duration-200 flex justify-center items-center" :disabled="form.processing">
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ form.processing ? 'Processing...' : 'Register' }}</span>
                </button>
                </div>
            </form>

            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white text-gray-500 font-medium">
                            Or sign up with
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <a href="#" class="w-full inline-flex justify-center items-center py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <span class="sr-only">Sign up with Google</span>
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                        </svg>
                        Google
                    </a>

                    <a href="#" class="w-full inline-flex justify-center items-center py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <span class="sr-only">Sign up with GitHub</span>
                        <svg class="w-5 h-5 mr-2 text-gray-800" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                        </svg>
                        GitHub
                    </a>
                </div>
            </div>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a :href="route('login')" class="font-medium text-purple-600 hover:text-purple-500 hover:underline transition-colors duration-200 ml-1">
                        Log in here
                    </a>
                </p>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { ref, computed, onMounted } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    referralCode: {
        type: String,
        default: '',
    },
});

// Create a reactive reference to store the referral code
const referralCode = ref(props.referralCode || '');

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('ref')) {
        referralCode.value = urlParams.get('ref');
    } else if (urlParams.has('referral_code')) {
        referralCode.value = urlParams.get('referral_code');
    }
    console.log('Referral code from URL:', referralCode.value);
});

const form = useForm({
    // Contact person details
    contact_title: 'Mister',
    contact_first_name: '',
    contact_last_name: '',
    organization_name: '',
    
    // Address details
    address: '',
    address_suffix: '',
    postal_code: '',
    location: '',
    country: 'Switzerland',
    
    // Contact details
    email: '',
    phone: '',
    
    // Account credentials
    password: '',
    password_confirmation: '',
    
    // Terms and newsletter
    terms: false,
    newsletter: false,
    
    // Referral code
    referral_code: '',
});

// Update the form's referral code when the ref changes
onMounted(() => {
    // Set the form's referral code after it's been read from URL
    setTimeout(() => {
        form.referral_code = referralCode.value;
        console.log('Form referral code set to:', form.referral_code);
    }, 0);
});

const hasReferralCode = computed(() => {
    return referralCode.value && referralCode.value.length > 0;
});

const submit = () => {
    // If we have a referral code, use the referral registration endpoint
    if (hasReferralCode.value) {
        console.log('Submitting with referral code:', form.referral_code);
        
        // Ensure the form has the latest referral code value
        form.referral_code = referralCode.value;
        
        form.post(route('register.with_referral'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
            onError: (errors) => {
                console.error('Registration errors:', errors);
            }
        });
    } else {
        console.log('Submitting without referral code');
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
            onError: (errors) => {
                console.error('Registration errors:', errors);
            }
        });
    }
};
</script>