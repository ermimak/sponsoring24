<template>
  <DashboardLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $t('License Management') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div v-if="!user.has_active_license" class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Purchase Annual License') }}</h3>
              
              <div class="bg-gray-50 p-6 rounded-lg mb-6">
                <div class="flex justify-between items-center mb-4">
                  <div>
                    <h4 class="font-medium text-gray-800">{{ $t('Fundoo Annual License') }}</h4>
                    <p class="text-gray-600 text-sm">{{ $t('Access to all premium features for one year') }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-lg font-bold" :class="{'line-through': user.discount_eligible && !user.discount_used}">
                      CHF {{ standardPrice.toFixed(2) }}
                    </p>
                    <p v-if="user.discount_eligible && !user.discount_used" class="text-green-600 font-bold">
                      CHF {{ discountedPrice.toFixed(2) }} 
                      <span class="text-sm font-normal">({{ $t('Referral discount applied') }})</span>
                    </p>
                  </div>
                </div>

                <div v-if="user.discount_eligible && !user.discount_used" class="bg-green-50 border border-green-200 p-3 rounded-md mb-4">
                  <p class="text-green-700 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
                      <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v8a1 1 0 11-2 0V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ $t('You are eligible for a CHF 50 discount on your first annual license!') }}
                  </p>
                </div>

                <div v-if="paymentProcessing" class="flex justify-center my-4">
                  <div class="spinner"></div>
                </div>
                
                <div v-else>
                  <div v-if="paymentError" class="bg-red-50 border border-red-200 p-3 rounded-md mb-4">
                    <p class="text-red-700 text-sm">{{ paymentError }}</p>
                  </div>

                  <div v-if="showStripeElement" ref="cardElement" class="my-4 p-3 border border-gray-300 rounded-md"></div>

                  <div class="flex justify-end">
                    <button 
                      v-if="!showStripeElement" 
                      @click="initializePayment" 
                      class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                      {{ $t('Purchase License') }}
                    </button>
                    
                    <button 
                      v-else 
                      @click="processPayment" 
                      :disabled="!stripeElementComplete || paymentProcessing"
                      class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                      {{ $t('Complete Purchase') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Your License') }}</h3>
              
              <div class="bg-gray-50 p-6 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                  <div>
                    <h4 class="font-medium text-gray-800">{{ $t('Fundoo Annual License') }}</h4>
                    <p class="text-gray-600 text-sm">{{ $t('Valid until') }}: {{ licenseExpiryDate }}</p>
                  </div>
                  <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $t('Active') }}
                  </div>
                </div>

                <div class="mt-4">
                  <p class="text-gray-700">{{ $t('License Key') }}: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ user.license_key || 'N/A' }}</span></p>
                </div>

                <div class="mt-6" v-if="daysUntilExpiry <= 30">
                  <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-md">
                    <p class="text-yellow-700 text-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                      {{ $t('Your license will expire in {days} days. Consider renewing soon.', { days: daysUntilExpiry }) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Share Your Referral Link') }}</h3>
              
              <div class="bg-gray-50 p-6 rounded-lg">
                <p class="text-gray-700 mb-4">
                  {{ $t('Share your referral link with others. When someone signs up using your link and purchases a license, you will receive CHF 100 in bonus credits, and they will get CHF 50 off their first annual license.') }}
                </p>
                
                <div class="flex items-center mt-4">
                  <input 
                    type="text" 
                    readonly 
                    :value="referralLink" 
                    class="flex-grow px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  />
                  <button 
                    @click="copyReferralLink" 
                    class="px-4 py-2 bg-gray-200 border border-gray-300 rounded-r-md hover:bg-gray-300 focus:outline-none"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                      <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                    </svg>
                  </button>
                </div>
                <p v-if="linkCopied" class="text-green-600 text-sm mt-2">{{ $t('Link copied to clipboard!') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { defineComponent } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { loadStripe } from '@stripe/stripe-js';
import { route } from 'ziggy-js';

export default defineComponent({
  components: {
    DashboardLayout,
  },
  
  props: {
    user: Object,
    stripePublishableKey: String,
    licenseData: Object,
  },
  
  data() {
    return {
      standardPrice: 500.00, // CHF 500
      discountAmount: 50.00, // CHF 50
      stripe: null,
      cardElement: null,
      stripeElementComplete: false,
      showStripeElement: false,
      paymentProcessing: false,
      paymentError: null,
      linkCopied: false,
      licenseExpiryDate: this.licenseData?.expires_at || null,
      daysUntilExpiry: this.calculateDaysUntilExpiry(),
    };
  },
  
  computed: {
    discountedPrice() {
      return this.standardPrice - this.discountAmount;
    },
    
    referralLink() {
      const baseUrl = window.location.origin;
      return `${baseUrl}/register?ref=${this.user.referral_code}`;
    },
  },
  
  methods: {
    calculateDaysUntilExpiry() {
      if (!this.licenseData?.expires_at) return null;
      
      const expiryDate = new Date(this.licenseData.expires_at);
      const today = new Date();
      const diffTime = expiryDate - today;
      return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    },
    
    async initializePayment() {
      this.showStripeElement = true;
      
      if (!this.stripe) {
        this.stripe = await loadStripe(this.stripePublishableKey);
      }
      
      this.$nextTick(() => {
        const elements = this.stripe.elements();
        
        this.cardElement = elements.create('card', {
          style: {
            base: {
              fontSize: '16px',
              color: '#32325d',
              fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
              '::placeholder': {
                color: '#aab7c4',
              },
            },
            invalid: {
              color: '#fa755a',
              iconColor: '#fa755a',
            },
          },
        });
        
        this.cardElement.mount(this.$refs.cardElement);
        
        this.cardElement.on('change', (event) => {
          this.stripeElementComplete = event.complete;
          if (event.error) {
            this.paymentError = event.error.message;
          } else {
            this.paymentError = null;
          }
        });
      });
    },
    
    async processPayment() {
      if (!this.stripeElementComplete || this.paymentProcessing) {
        return;
      }
      
      this.paymentProcessing = true;
      this.paymentError = null;
      
      try {
        // Create payment intent on the server
        const response = await axios.post(route('dashboard.license.payment-intent'), {
          apply_discount: this.user.discount_eligible && !this.user.discount_used
        });
        
        const { client_secret } = response.data;
        
        // Confirm payment with Stripe
        const result = await this.stripe.confirmCardPayment(client_secret, {
          payment_method: {
            card: this.cardElement,
            billing_details: {
              email: this.user.email,
              name: this.user.name,
            },
          },
        });
        
        if (result.error) {
          // Show error to customer
          this.paymentError = result.error.message;
          this.paymentProcessing = false;
        } else {
          // Payment succeeded, reload the page to show updated license status
          window.location.reload();
        }
      } catch (error) {
        this.paymentError = error.response?.data?.message || 'An error occurred while processing your payment.';
        this.paymentProcessing = false;
      }
    },
    
    copyReferralLink() {
      navigator.clipboard.writeText(this.referralLink).then(() => {
        this.linkCopied = true;
        setTimeout(() => {
          this.linkCopied = false;
        }, 3000);
      });
    },
  },
  
  beforeUnmount() {
    if (this.cardElement) {
      this.cardElement.destroy();
    }
  },
});
</script>

<style scoped>
.spinner {
  border: 3px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 3px solid #3498db;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
