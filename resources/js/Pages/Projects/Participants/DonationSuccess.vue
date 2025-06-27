<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md mt-10">
      <div class="flex justify-center mb-6">
        <div class="bg-green-100 rounded-full p-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
      </div>
      
      <h1 class="text-3xl font-bold text-center mb-4">Thank You!</h1>
      <p class="text-center text-gray-600 mb-6">
        Your payment of {{ donation.amount }} {{ donation.currency }} has been successfully processed.
      </p>
      
      <div class="bg-gray-50 p-6 rounded-lg mb-6">
        <h2 class="text-lg font-semibold mb-3">Payment Details</h2>
        <div class="grid grid-cols-2 gap-2">
          <div class="text-gray-600">Amount:</div>
          <div class="font-medium">{{ donation.amount }} {{ donation.currency }}</div>
          
          <div class="text-gray-600">Payment Method:</div>
          <div class="font-medium">{{ formatPaymentMethod(paymentMethod) }}</div>
          
          <div class="text-gray-600">Date:</div>
          <div class="font-medium">{{ formatDate(donation.paid_at || donation.updated_at) }}</div>
          
          <div class="text-gray-600">Reference:</div>
          <div class="font-medium">{{ donation.id }}</div>
        </div>
      </div>
      
      <div class="bg-blue-50 p-4 rounded-lg mb-6">
        <p class="text-center text-blue-800">
          A confirmation email has been sent to {{ donation.supporter_email }}
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
import { usePage } from '@inertiajs/vue3';
import {route} from 'ziggy-js';
const page = usePage();

const props = defineProps({
  project: Object,
  participant: Object,
  donation: Object,
  paymentMethod: String,
});

function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function formatPaymentMethod(method) {
  if (!method) return 'Online Payment';
  
  const methods = {
    'card': 'Credit/Debit Card',
    'twint': 'TWINT',
    'invoice': 'Invoice',
  };
  
  return methods[method.toLowerCase()] || method;
}
</script>
