<template>
  <AdminLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $t('Referral Management') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm font-medium">Total Referrals</div>
            <div class="text-3xl font-bold text-gray-800">{{ stats.totalReferrals }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm font-medium">Credited Referrals</div>
            <div class="text-3xl font-bold text-green-600">{{ stats.totalCredited }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm font-medium">Pending Referrals</div>
            <div class="text-3xl font-bold text-yellow-500">{{ stats.totalPending }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm font-medium">Total Amount Credited</div>
            <div class="text-3xl font-bold text-blue-600">CHF {{ stats.totalAmount ? Number(stats.totalAmount).toFixed(2) : '0.00' }}</div>
          </div>
        </div>

        <!-- Top Referrers and Monthly Trends -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Top Referrers -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Top Referrers</h3>
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referrals</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Earned</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="referrer in topReferrers" :key="referrer.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ referrer.name }}</div>
                        <div class="text-sm text-gray-500">{{ referrer.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ referrer.referral_count }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">CHF {{ referrer.total_earned }}</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Monthly Trends -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Monthly Referral Trends</h3>
            <div class="h-64">
              <!-- Chart would go here - simplified for now -->
              <div class="flex h-full items-end">
                <div v-for="trend in monthlyTrends" :key="trend.month" class="flex-1 mx-1">
                  <div class="bg-blue-500 rounded-t" :style="{ height: (trend.count / maxTrendCount * 100) + '%' }"></div>
                  <div class="text-xs text-center mt-1">{{ formatMonth(trend.month) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Referrals Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">All Referrals</h3>
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referrer</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referred User</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="referral in referrals.data" :key="referral.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ referral.user.name }}</div>
                        <div class="text-sm text-gray-500">{{ referral.user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ referral.referredUser ? referral.referredUser.name : 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ referral.referredUser ? referral.referredUser.email : 'N/A' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(referral.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">CHF {{ referral.amount ? Number(referral.amount).toFixed(2) : '0.00' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(referral.status)">
                      {{ referral.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <form @submit.prevent="updateStatus(referral.id, 'credited')" v-if="referral.status === 'pending'">
                      <button type="submit" class="text-green-600 hover:text-green-900 mr-2">Credit</button>
                    </form>
                    <form @submit.prevent="updateStatus(referral.id, 'rejected')" v-if="referral.status === 'pending'">
                      <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                    </form>
                  </td>
                </tr>
              </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="mt-4">
              <Pagination :links="referrals.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import { defineComponent } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useForm } from '@inertiajs/vue3';
import { route } from '@/ziggy-plugin';

export default defineComponent({
  components: {
    AdminLayout,
    Pagination
  },
  
  props: {
    referrals: Object,
    stats: Object,
    topReferrers: Array,
    monthlyTrends: Array
  },
  
  computed: {
    maxTrendCount() {
      if (!this.monthlyTrends || this.monthlyTrends.length === 0) return 1;
      return Math.max(...this.monthlyTrends.map(trend => trend.count));
    }
  },
  
  methods: {
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString();
    },
    
    formatMonth(monthString) {
      const [year, month] = monthString.split('-');
      return `${month}/${year.slice(2)}`;
    },
    
    getStatusClass(status) {
      const classes = {
        pending: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800',
        credited: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800',
        rejected: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'
      };
      
      return classes[status] || '';
    },
    
    updateStatus(id, status) {
      const form = useForm({
        status: status
      });
      
      form.post(route('admin.referrals.update-status', { bonusCredit: id }), {
        preserveScroll: true
      });
    }
  }
});
</script>
