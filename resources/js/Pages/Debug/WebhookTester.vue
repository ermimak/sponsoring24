<template>
  <DashboardLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Webhook Tester
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Stripe Webhook Tester</h3>
            
            <div class="mb-4">
              <p class="text-gray-600 mb-2">
                This tool allows you to simulate Stripe webhooks for testing purposes.
                Select an event type and provide the necessary data to send a test webhook to your application.
              </p>
            </div>
            
            <div class="mb-4">
              <label for="eventType" class="block text-sm font-medium text-gray-700">Event Type</label>
              <select 
                id="eventType" 
                v-model="eventType"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="checkout.session.completed">checkout.session.completed</option>
                <option value="payment_intent.succeeded">payment_intent.succeeded</option>
              </select>
            </div>
            
            <div class="mb-4">
              <label for="userId" class="block text-sm font-medium text-gray-700">User ID</label>
              <input 
                type="text" 
                id="userId" 
                v-model="userId"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Enter user ID"
              />
            </div>
            
            <div class="mb-4">
              <label for="paymentId" class="block text-sm font-medium text-gray-700">Payment ID</label>
              <input 
                type="text" 
                id="paymentId" 
                v-model="paymentId"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Enter payment ID or generate random"
              />
              <button 
                @click="generateRandomId"
                class="mt-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Generate Random ID
              </button>
            </div>
            
            <div class="mb-4">
              <label for="amount" class="block text-sm font-medium text-gray-700">Amount (CHF)</label>
              <input 
                type="number" 
                id="amount" 
                v-model="amount"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Enter amount in CHF"
              />
            </div>
            
            <div class="mb-4">
              <label class="flex items-center">
                <input type="checkbox" v-model="applyDiscount" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                <span class="ml-2 text-sm text-gray-600">Apply Discount</span>
              </label>
            </div>
            
            <div class="mb-4">
              <label for="endpoint" class="block text-sm font-medium text-gray-700">Webhook Endpoint</label>
              <select 
                id="endpoint" 
                v-model="endpoint"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="/webhook/license/stripe">License Webhook (/webhook/license/stripe)</option>
              </select>
            </div>
            
            <div class="mb-4">
              <button 
                @click="sendWebhook"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                <span v-if="processing">Processing...</span>
                <span v-else>Send Test Webhook</span>
              </button>
            </div>
            
            <div v-if="result" class="mt-6">
              <h4 class="text-md font-medium text-gray-900 mb-2">Result</h4>
              <div class="bg-gray-100 p-4 rounded-md">
                <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ JSON.stringify(result, null, 2) }}</pre>
              </div>
            </div>
            
            <div v-if="error" class="mt-6">
              <div class="bg-red-50 border border-red-200 p-4 rounded-md">
                <h4 class="text-md font-medium text-red-800 mb-2">Error</h4>
                <p class="text-sm text-red-700">{{ error }}</p>
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
import axios from 'axios';

export default defineComponent({
  components: {
    DashboardLayout,
  },
  
  data() {
    return {
      eventType: 'checkout.session.completed',
      userId: '',
      paymentId: '',
      amount: 500,
      applyDiscount: false,
      endpoint: '/webhook/license/stripe',
      processing: false,
      result: null,
      error: null,
    };
  },
  
  mounted() {
    // Try to get the current user's ID
    if (this.$page.props.auth && this.$page.props.auth.user) {
      this.userId = this.$page.props.auth.user.id;
    }
  },
  
  methods: {
    generateRandomId() {
      const randomId = 'test_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
      this.paymentId = randomId;
    },
    
    async sendWebhook() {
      this.processing = true;
      this.result = null;
      this.error = null;
      
      try {
        // Create webhook payload based on selected event type
        const payload = this.createWebhookPayload();
        
        // Send the webhook to the specified endpoint
        const response = await axios.post(this.endpoint, payload, {
          headers: {
            'Content-Type': 'application/json',
            'Stripe-Signature': 'test_signature' // This will be ignored in dev mode
          }
        });
        
        this.result = response.data;
        
        // Check if license was created
        await this.checkLicenseCreation();
      } catch (error) {
        console.error('Webhook test error:', error);
        this.error = error.response?.data?.error || error.message || 'An unknown error occurred';
      } finally {
        this.processing = false;
      }
    },
    
    createWebhookPayload() {
      const now = Math.floor(Date.now() / 1000);
      const discountAmount = this.applyDiscount ? 50 : 0;
      const finalAmount = this.amount - discountAmount;
      
      if (this.eventType === 'checkout.session.completed') {
        return {
          id: 'evt_' + Math.random().toString(36).substring(2, 15),
          object: 'event',
          api_version: '2020-08-27',
          created: now,
          data: {
            object: {
              id: 'cs_' + Math.random().toString(36).substring(2, 15),
              object: 'checkout.session',
              amount_total: finalAmount * 100, // in cents
              currency: 'chf',
              customer_email: 'test@example.com',
              payment_intent: this.paymentId || ('pi_' + Math.random().toString(36).substring(2, 15)),
              payment_status: 'paid',
              metadata: {
                user_id: this.userId,
                license_type: 'annual',
                discount_applied: this.applyDiscount ? 'yes' : 'no',
                discount_amount: discountAmount
              }
            }
          },
          type: 'checkout.session.completed'
        };
      } else if (this.eventType === 'payment_intent.succeeded') {
        return {
          id: 'evt_' + Math.random().toString(36).substring(2, 15),
          object: 'event',
          api_version: '2020-08-27',
          created: now,
          data: {
            object: {
              id: this.paymentId || ('pi_' + Math.random().toString(36).substring(2, 15)),
              object: 'payment_intent',
              amount: finalAmount * 100, // in cents
              currency: 'chf',
              payment_method_types: ['card'],
              metadata: {
                user_id: this.userId,
                license_type: 'annual',
                discount_applied: this.applyDiscount ? 'yes' : 'no',
                discount_amount: discountAmount
              }
            }
          },
          type: 'payment_intent.succeeded'
        };
      }
      
      return {};
    },
    
    async checkLicenseCreation() {
      try {
        // Wait a moment for the license to be created
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Make a direct API call to check for licenses
        const response = await axios.get('/api/check-license-status');
        
        this.result = {
          ...this.result,
          license_check_response: response.data
        };
        
        if (response.data && response.data.has_active_license) {
          this.result.license_created = true;
          this.result.license = response.data.license;
        } else {
          this.result.license_created = false;
          this.result.message = 'No active license found';
        }
      } catch (error) {
        console.error('Error checking license creation:', error);
        this.result = {
          ...this.result,
          license_created: false,
          check_error: error.message
        };
      }
    }
  }
});
</script>
