<template>
  <DashboardLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $t('Referrals & Earnings') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500">{{ $t('Total Referrals') }}</div>
                  <div class="text-2xl font-semibold">{{ stats.totalReferrals }}</div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500">{{ $t('Total Earned') }}</div>
                  <div class="text-2xl font-semibold">CHF {{ stats.totalEarned.toFixed(2) }}</div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500">{{ $t('Conversion Rate') }}</div>
                  <div class="text-2xl font-semibold">{{ stats.conversionRate }}%</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Referral Link -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Your Referral Link') }}</h3>
            <p class="text-gray-600 mb-4">{{ $t('Share this link with others to earn CHF 100 when they purchase a license.') }}</p>
            
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
            
            <div class="mt-6 flex flex-wrap gap-4">
              <a 
                :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(referralLink)}`"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                {{ $t('Share on Facebook') }}
              </a>
              
              <a 
                :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(referralLink)}&text=${encodeURIComponent('Join Fundoo and get CHF 50 off your first annual license!')}`"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-blue-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-600 focus:outline-none focus:border-blue-600 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                {{ $t('Share on Twitter') }}
              </a>
              
              <a 
                :href="`mailto:?subject=${encodeURIComponent('Join Fundoo and save CHF 50')}&body=${encodeURIComponent('I thought you might be interested in Fundoo. Sign up using my referral link and get CHF 50 off your first annual license: ' + referralLink)}`"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                {{ $t('Share via Email') }}
              </a>
            </div>
          </div>
        </div>

        <!-- Referral History -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Referral History') }}</h3>
            
            <div v-if="referrals.length === 0" class="bg-gray-50 p-6 rounded-lg text-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <p class="text-gray-600">{{ $t('You haven\'t referred anyone yet. Share your referral link to start earning!') }}</p>
            </div>
            
            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('User') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Date') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Status') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Amount') }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="referral in referrals" :key="referral.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                          <span class="text-gray-700 font-medium">{{ referral.user.name.charAt(0) }}</span>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">{{ referral.user.name }}</div>
                          <div class="text-sm text-gray-500">{{ referral.user.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ formatDate(referral.created_at) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span 
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                        :class="{
                          'bg-green-100 text-green-800': referral.status === 'credited',
                          'bg-yellow-100 text-yellow-800': referral.status === 'pending',
                          'bg-gray-100 text-gray-800': referral.status === 'expired'
                        }"
                      >
                        {{ referral.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      CHF {{ referral.amount.toFixed(2) }}
                    </td>
                  </tr>
                </tbody>
              </table>
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
import { ref } from 'vue';
import { route } from '@/ziggy-plugin';

export default defineComponent({
  components: {
    DashboardLayout,
  },
  
  props: {
    user: Object,
    referrals: Array,
    stats: Object,
  },
  
  setup(props) {
    const linkCopied = ref(false);
    
    const referralLink = `${window.location.origin}/register?ref=${props.user.referral_code}`;
    
    const copyReferralLink = () => {
      navigator.clipboard.writeText(referralLink).then(() => {
        linkCopied.value = true;
        setTimeout(() => {
          linkCopied.value = false;
        }, 3000);
      });
    };
    
    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      }).format(date);
    };
    
    return {
      linkCopied,
      referralLink,
      copyReferralLink,
      formatDate,
    };
  },
});
</script>
