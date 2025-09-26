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
          <div id="card-element" class="p-3 border rounded-lg mb-4 bg-white min-h-[40px]"></div>
          <div id="card-errors" class="text-red-500 text-sm mb-4" role="alert"></div>
          <button type="submit" 
                  :disabled="processing"
                  class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed">
            {{ processing ? 'Processing...' : `Pay ${donation.amount} ${donation.currency}` }}
          </button>
        </form>
      </div>

      <!-- TWINT Payment Form -->
      <div v-if="selectedMethod === 'twint'" class="mt-6">
        <div class="bg-blue-50 p-4 rounded-lg mb-4">
          <p class="text-center">TWINT payment will be processed via Stripe.</p>
          <div class="flex justify-center my-4">
            <div class="h-12 flex items-center justify-center bg-blue-500 text-white font-bold rounded px-4 py-2">
              TWINT
            </div>
          </div>
        </div>
        <button @click="handleTwintPayment" 
                :disabled="processing"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
          {{ processing ? 'Processing...' : `Pay with TWINT` }}
        </button>
      </div>

      <!-- Invoice Payment Form -->
      <div v-if="selectedMethod === 'invoice'" class="mt-6">
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
          <p class="text-center">An invoice will be sent to your email address.</p>
          <p class="text-center text-sm text-gray-500 mt-2">Please provide your billing information below:</p>
        </div>
        <form @submit.prevent="handleInvoicePayment">
          <div class="mb-4">
            <label for="billing-name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" id="billing-name" v-model="invoiceData.name" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
          </div>
          <div class="mb-4">
            <label for="billing-address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <input type="text" id="billing-address" v-model="invoiceData.address" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
          </div>
          <div class="mb-4">
            <label for="billing-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="billing-email" v-model="invoiceData.email" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
          </div>
          <button type="submit" 
                  :disabled="processing"
                  class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed">
            {{ processing ? 'Processing...' : 'Request Invoice' }}
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
import { ref, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { loadStripe } from '@stripe/stripe-js'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  project: Object,
  participant: Object,
  donation: Object,
})

const page = usePage()

const selectedMethod = ref(null)
const processing = ref(false)
const stripe = ref(null)
const elements = ref(null)
const cardElement = ref(null)
const invoiceData = ref({
  name: '',
  address: '',
  email: props.donation?.supporter_email || '',
})

onMounted(async () => {
  try {
    // Check if Stripe key is available from Inertia shared props, environment variables, or fallback
    const stripeKey = page.props.stripeKey || window.config?.stripeKey || import.meta.env.VITE_STRIPE_PUBLIC_KEY;
    console.log('Using Stripe key:', stripeKey);
    if (!stripeKey) {
      console.error('Stripe public key is not configured. Please check your environment variables.');
      return;
    }

    // Initialize Stripe
    stripe.value = await loadStripe(stripeKey);
    if (!stripe.value) {
      console.error('Failed to initialize Stripe');
      return;
    }
    
    // Create elements instance
    elements.value = stripe.value.elements();
  } catch (error) {
    console.error('Error initializing Stripe:', error);
  }
});

// Watch for selectedMethod changes to mount the card element when card payment is selected
watch(selectedMethod, (newMethod) => {
  if (newMethod === 'card' && stripe.value && elements.value && !cardElement.value) {
    // Wait for the DOM to update
    setTimeout(() => {
      try {
        // Create card element
        cardElement.value = elements.value.create('card', {
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
        });
        
        // Check if the element exists in the DOM
        const cardElementContainer = document.getElementById('card-element');
        if (cardElementContainer) {
          // Mount the card element
          cardElement.value.mount('#card-element');
          
          // Handle real-time validation errors
          cardElement.value.on('change', (event) => {
            const displayError = document.getElementById('card-errors');
            if (displayError) {
              if (event.error) {
                displayError.textContent = event.error.message;
              } else {
                displayError.textContent = '';
              }
            }
          });
        } else {
          console.error('Card element container not found in DOM');
        }
      } catch (error) {
        console.error('Error mounting card element:', error);
      }
    }, 100); // Small delay to ensure DOM is updated
  }
});

function selectPaymentMethod(method) {
  selectedMethod.value = method
}

async function handleCardPayment() {
  if (!stripe.value || !cardElement.value) {
    console.error('Stripe not initialized')
    return
  }

  processing.value = true
  const errorElement = document.getElementById('card-errors');

  try {
    // Create payment intent on the server
    const response = await axios.post('/api/payments/create-payment-intent', {
      amount: props.donation.amount,
      currency: props.donation.currency.toLowerCase(),
      donation_id: props.donation.id,
      payment_method: 'card'
    });

    const { clientSecret } = response.data;

    if (!clientSecret) {
      throw new Error('No client secret returned from the server');
    }

    // Confirm the payment
    const result = await stripe.value.confirmCardPayment(clientSecret, {
      payment_method: {
        card: cardElement.value,
        billing_details: {
          email: props.donation.supporter_email,
        },
      },
    });

    if (result.error) {
      // Show error to customer
      errorElement.textContent = result.error.message;
    } else {
      // Payment successful
      if (result.paymentIntent.status === 'succeeded') {
        // Finalize payment server-side to update donation, avoiding webhook race conditions
        try {
          await axios.post('/api/payments/finalize', {
            donation_id: props.donation.id,
            payment_intent_id: result.paymentIntent.id,
          });
        } catch (e) {
          console.error('Finalize payment failed:', e);
          // Proceed to success page even if finalize fallback fails
        }
        // Redirect to success page
        router.visit(route('participant.donate.success', {
          projectId: props.project.id,
          participantId: props.participant.id,
          donationId: props.donation.id,
        }));
      }
    }
  } catch (error) {
    console.error('Payment failed:', error);
    errorElement.textContent = error.response?.data?.error || 'An error occurred while processing your payment. Please try again.';
  } finally {
    processing.value = false;
  }
}

async function handleTwintPayment() {
  processing.value = true;
  
  try {
    // Create payment intent for TWINT
    const response = await axios.post('/api/payments/create-payment-intent', {
      amount: props.donation.amount,
      currency: props.donation.currency.toLowerCase(),
      donation_id: props.donation.id,
      payment_method: 'twint'
    });

    const { clientSecret } = response.data;

    if (!clientSecret) {
      throw new Error('No client secret returned from the server');
    }

    // Redirect to Stripe Checkout for TWINT payment using redirectToCheckout
    // This is the recommended approach for TWINT according to Stripe docs
    const { error } = await stripe.value.redirectToCheckout({
      paymentIntentClientSecret: clientSecret,
      successUrl: window.location.origin + route('participant.donate.success', {
        projectId: props.project.id,
        participantId: props.participant.id,
        donationId: props.donation.id,
      }),
      cancelUrl: window.location.origin + route('participant.donate.payment', {
        projectId: props.project.id,
        participantId: props.participant.id,
        donationId: props.donation.id,
      }),
    });

    if (error) {
      throw error;
    }
  } catch (error) {
    console.error('TWINT payment failed:', error);
    alert('TWINT payment failed: ' + (error.message || 'Unknown error'));
    processing.value = false;
  }
}

async function handleInvoicePayment() {
  processing.value = true;
  
  try {
    // Send invoice request to server
    await axios.post('/api/payments/request-invoice', {
      donation_id: props.donation.id,
      billing_info: invoiceData.value
    });
    
    // Redirect to success page
    router.visit(route('participant.donate.invoice.success', {
      projectId: props.project.id,
      participantId: props.participant.id,
      donationId: props.donation.id,
    }));
  } catch (error) {
    console.error('Invoice request failed:', error);
    alert('Failed to request invoice: ' + (error.response?.data?.error || 'Unknown error'));
  } finally {
    processing.value = false;
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
