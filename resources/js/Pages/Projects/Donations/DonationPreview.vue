<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center py-10">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden max-w-3xl w-full">
      <!-- Header Image and Logo -->
      <div class="relative h-64 bg-cover bg-center" :style="{ backgroundImage: `url(${project.image_landscape})` }">
        <div class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-md">
          <img src="/images/fundoo-logo.png" alt="fundoo logo" class="h-8 w-8" />
        </div>
      </div>

      <div class="p-8">
        <!-- Donation Title and Status -->
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Donation accounting – {{ project.name }}</h1>
        <p class="text-sm text-gray-600 mb-6">Status: <span class="font-semibold text-green-600">{{ donation.status }}</span></p>

        <!-- Supporter Information -->
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mb-6">
          <div>
            <p>{{ supporter.name }}</p>
            <p>{{ supporter.address }}</p>
            <p>{{ supporter.postal_code }} {{ supporter.city }}</p>
            <p>{{ supporter.country }}</p>
            <p>{{ supporter.email }}</p>
          </div>
          <div class="text-right">
            <p>number</p>
            <p class="font-semibold">{{ String(donation.id).substring(0, 8) }}</p>
          </div>
        </div>

        <!-- Donation Statement Table -->
        <h2 class="text-xl font-bold text-gray-900 mb-4">Donation statement {{ String(donation.id).substring(0, 8) }}</h2>
        <table class="min-w-full divide-y divide-gray-200 mb-6">
          <thead>
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pcs.</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">In total</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-4 py-2 whitespace-nowrap">Donation for {{ donation.participant_name }}</td>
              <td class="px-4 py-2 whitespace-nowrap text-right">1</td>
              <td class="px-4 py-2 whitespace-nowrap text-right">lump sum</td>
              <td class="px-4 py-2 whitespace-nowrap text-right">{{ donation.currency }} {{ parseFloat(donation.amount).toFixed(2) }}</td>
              <td class="px-4 py-2 whitespace-nowrap text-right">{{ donation.currency }} {{ parseFloat(donation.amount).toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>

        <!-- Open Donation -->
        <div class="flex justify-between items-center border-t border-gray-200 pt-4">
          <p class="text-lg font-semibold text-gray-900">Open donation</p>
          <p class="text-lg font-bold text-purple-700">{{ donation.currency }} {{ parseFloat(donation.amount).toFixed(2) }}</p>
        </div>
      </div>

      <!-- Voluntary Rounding Up Section -->
      <div v-if="showRoundingUp" class="bg-gray-50 p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Would you like to voluntarily round up the contribution?</h2>
        <p class="text-sm text-gray-600 mb-6">By voluntarily rounding up your contribution, you will help the organizer <strong>{{ project.name }}</strong> to reduce the costs of processing and payment fees for the {{ project.name }} project.</p>

        <h3 class="text-lg font-semibold text-gray-900 mb-4">Voluntary rounding up</h3>
        
        <!-- Placeholder for Rounding Up Slider -->
        <div class="h-10 bg-gray-200 rounded-lg mb-6"></div>

        <div class="flex justify-between items-center text-sm text-gray-700 mb-4">
            <span>0 %</span>
            <span>CHF {{ parseFloat(roundingUpAmount).toFixed(2) }}</span>
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-4">Thank you very much!</h3>
        
        <div class="flex justify-between items-center text-sm text-gray-700 mb-2">
            <span>Your contribution</span>
            <span>{{ donation.currency }} {{ parseFloat(donation.amount).toFixed(2) }}</span>
        </div>
        <div class="flex justify-between items-center text-sm text-gray-700 mb-4">
            <span>Voluntary rounding up</span>
            <span>{{ donation.currency }} {{ parseFloat(roundingUpAmount).toFixed(2) }}</span>
        </div>

        <div class="flex justify-between items-center border-t border-gray-200 pt-4">
            <p class="text-lg font-semibold text-gray-900">Total:</p>
            <p class="text-xl font-bold text-purple-700">{{ donation.currency }} {{ (parseFloat(donation.amount) + parseFloat(roundingUpAmount)).toFixed(2) }}</p>
        </div>
        
        <!-- To Pay Button -->
        <div class="mt-6">
            <button class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg text-lg">To pay</button>
        </div>
      </div>

      <!-- Contact Information -->
      <div class="p-8 text-center text-gray-600 text-sm">
        <p>If you have any questions about donation accounting, please contact <a href="mailto:reto.schaufelberger@chesselhuus.ch" class="text-purple-600 hover:underline">reto.schaufelberger@chesselhuus.ch</a> directly.</p>
      </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center text-gray-500 text-sm">
      <p>Powered by fundoo © 2025</p>
      <p><a href="#" class="text-gray-500 hover:underline">info@fundoo.ch</a> - <a href="#" class="text-gray-500 hover:underline">Datenschutzerklärung</a></p>
    </div>

    <!-- Cookie Consent Banner (Placeholder) -->
    <div class="fixed bottom-4 right-4 bg-gray-800 text-white p-4 rounded-lg shadow-lg text-sm">
        <p>We use cookies!</p>
        <p class="text-xs">We use cookies to analyze our website traffic and improve your user experience. For detailed information and individual settings, click<a href="#" class="text-purple-400 hover:underline">here</a></p>
        <div class="mt-2 flex justify-end space-x-2">
            <button class="bg-gray-600 hover:bg-gray-700 px-3 py-1 rounded">Only necessary cookies</button>
            <button class="bg-purple-600 hover:bg-purple-700 px-3 py-1 rounded">Accept all</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  donation: Object,
  project: Object,
  supporter: Object,
  showRoundingUp: Boolean,
  roundingUpAmount: [Number, String],
})

// Dummy data/methods for interactivity if needed later
// const roundedAmount = ref(0)
// const totalAmount = computed(() => parseFloat(props.donation.amount) + parseFloat(roundedAmount.value))

// function updateRoundedAmount(event) {
//   roundedAmount.value = event.target.value
// }
</script>

<style scoped>
/* Add any necessary scoped styles here */
</style> 