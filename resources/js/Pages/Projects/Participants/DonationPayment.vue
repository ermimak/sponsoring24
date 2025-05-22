<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md mt-10">
      <h1 class="text-3xl font-bold text-center mb-6">
        Pay {{ donation.currency }} {{ donation.amount }} now via
      </h1>
      <div class="space-y-4 mb-8">
        <button class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-100 transition">
          <span class="mr-3"><svg width="24" height="24" fill="none" stroke="currentColor"><rect x="4" y="4" width="16" height="16" rx="2" stroke-width="2"/></svg></span>
          The invoice
        </button>
        <button class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-100 transition">
          <span class="mr-3"><svg width="24" height="24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg></span>
          TWINT
        </button>
        <button class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-100 transition">
          <span class="mr-3"><svg width="24" height="24" fill="none" stroke="currentColor"><rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2"/><rect x="6" y="10" width="4" height="4" stroke-width="2"/></svg></span>
          Credit or debit card
        </button>
      </div>
      <div class="flex justify-center">
        <button @click="goBack" class="border border-red-500 text-red-600 px-6 py-2 rounded-lg flex items-center hover:bg-red-50 transition">
          <span class="mr-2">&#8592;</span> Back
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  project: Object,
  participant: Object,
  donation: Object,
})

const router = useRouter ? useRouter() : null
const page = usePage()

function goBack() {
  window.history.back()
}
</script>

<style scoped>
button {
  font-size: 1rem;
}
</style> 