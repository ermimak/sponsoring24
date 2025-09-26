<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md mt-10">
      <div class="flex justify-center mb-6">
        <div class="bg-blue-100 rounded-full p-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      
      <h1 class="text-3xl font-bold text-center mb-4">Invoice Requested</h1>
      <p class="text-center text-gray-600 mb-6">
        Your invoice request for {{ donation.amount }} {{ donation.currency }} has been successfully submitted.
      </p>
      
      <div class="bg-gray-50 p-6 rounded-lg mb-6">
        <h2 class="text-lg font-semibold mb-3">Invoice Details</h2>
        <div class="grid grid-cols-2 gap-2">
          <div class="text-gray-600">Amount:</div>
          <div class="font-medium">{{ donation.amount }} {{ donation.currency }}</div>
          
          <div class="text-gray-600">Billing Name:</div>
          <div class="font-medium">{{ donation.billing_name }}</div>
          
          <div class="text-gray-600">Email:</div>
          <div class="font-medium">{{ donation.billing_email || donation.supporter_email }}</div>
          
          <div class="text-gray-600">Reference:</div>
          <div class="font-medium">{{ donation.id }}</div>
        </div>
      </div>
      
      <div class="bg-yellow-50 p-4 rounded-lg mb-6">
        <p class="text-center text-yellow-800">
          <span class="font-medium">What happens next?</span><br>
          An invoice will be sent to your email address shortly. Please follow the payment instructions in the invoice.
        </p>
      </div>
      
      <div class="flex flex-col space-y-4">
        <a :href="route('participant.landing', { projectId: project.id, participantId: participant.id })" 
           class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 text-center">
          Return to {{ participant.name }}'s Page
        </a>
        
        <a :href="route('home')" 
           class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 text-center">
          Return to Homepage
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import {route} from 'ziggy-js';
const props = defineProps({
  project: Object,
  participant: Object,
  donation: Object,
});
</script>
