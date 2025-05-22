<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md mt-10">
      <h1 class="text-3xl font-bold text-center mb-6">
        Pay {{ donation.amount }} {{ donation.currency }} now via
      </h1>
      <div class="space-y-4 mb-8">
        <button @click="selectPaymentMethod('invoice')" 
                :class="{'bg-purple-50 border-purple-500': selectedMethod === 'invoice'}"
                class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-100 transition">
          <span class="mr-3"><svg width="24" height="24" fill="none" stroke="currentColor"><rect x="4" y="4" width="16" height="16" rx="2" stroke-width="2"/></svg></span>
          The invoice
        </button>
        <button @click="selectPaymentMethod('twint')"
                :class="{'bg-purple-50 border-purple-500': selectedMethod === 'twint'}"
                class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-100 transition">
          <span class="mr-3"><svg width="24" height="24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg></span>
          TWINT
        </button>
        <button @click="selectPaymentMethod('card')"
                :class="{'bg-purple-50 border-purple-500': selectedMethod === 'card'}"
                class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-100 transition">
          <span class="mr-3"><svg width="24" height="24" fill="none" stroke="currentColor"><rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2"/><rect x="6" y="10" width="4" height="4" stroke-width="2"/></svg></span>
          Credit or debit card
        </button>
      </div>

      <!-- Credit Card Payment Form -->
      <div v-if="selectedMethod === 'card'" class="mt-6">
        <form id="payment-form" @submit.prevent="handleCardPayment">
          <div id="card-element" class="p-3 border rounded-lg mb-4"></div>
          <div id="card-errors" class="text-red-500 text-sm mb-4" role="alert"></div>
          <button type="submit" 
                  :disabled="processing"
                  class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed">
            {{ processing ? 'Processing...' : `Pay ${donation.amount} ${donation.currency}` }}
          </button>
        </form>
      </div>

      <div class="flex justify-center mt-6">
        <button @click="goBack" class="border border-red-500 text-red-600 px-6 py-2 rounded-lg flex items-center hover:bg-red-50 transition">
          <span class="mr-2">&#8592;</span> Back
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { loadStripe } from '@stripe/stripe-js'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  project: Object,
  participant: Object,
  donation: Object,
})

const page = usePage()

const selectedMethod = ref(null)
const processing = ref(false)
const stripe = ref(null)
const cardElement = ref(null)

onMounted(async () => {
  // Initialize Stripe
  stripe.value = await loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY)
  
  // Create card element
  const elements = stripe.value.elements()
  cardElement.value = elements.create('card', {
    style: {
      base: {
        fontSize: '16px',
        color: '#424770',
        '::placeholder': {
          color: '#aab7c4',
        },
      },
      invalid: {
        color: '#9e2146',
      },
    },
  })
  
  // Mount the card element
  cardElement.value.mount('#card-element')
  
  // Handle real-time validation errors
  cardElement.value.on('change', (event) => {
    const displayError = document.getElementById('card-errors')
    if (event.error) {
      displayError.textContent = event.error.message
    } else {
      displayError.textContent = ''
    }
  })
})

function selectPaymentMethod(method) {
  selectedMethod.value = method
}

async function handleCardPayment() {
  if (!stripe.value || !cardElement.value) {
    console.error('Stripe not initialized')
    return
  }

  processing.value = true

  try {
    // Create payment intent on the server
    const response = await fetch('/api/create-payment-intent', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        amount: props.donation.amount,
        currency: props.donation.currency.toLowerCase(),
        donation_id: props.donation.id,
      }),
    })

    const { clientSecret } = await response.json()

    // Confirm the payment
    const result = await stripe.value.confirmCardPayment(clientSecret, {
      payment_method: {
        card: cardElement.value,
        billing_details: {
          email: props.donation.supporter_email,
        },
      },
    })

    if (result.error) {
      // Handle error
      const errorElement = document.getElementById('card-errors')
      errorElement.textContent = result.error.message
    } else {
      // Payment successful
      if (result.paymentIntent.status === 'succeeded') {
        // Redirect to success page or show success message
        router.visit(route('participant.donate.success', {
          projectId: props.project.id,
          participantId: props.participant.id,
          donationId: props.donation.id,
        }))
      }
    }
  } catch (error) {
    console.error('Payment failed:', error)
    const errorElement = document.getElementById('card-errors')
    errorElement.textContent = 'An error occurred while processing your payment. Please try again.'
  } finally {
    processing.value = false
  }
}

function goBack() {
  window.history.back()
}
</script>

<style scoped>
button {
  font-size: 1rem;
}

#card-element {
  background: white;
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}
</style> 