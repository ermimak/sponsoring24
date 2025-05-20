<template>
  <DashboardLayout>
    <template #breadcrumbs></template>
    <div>
      <h1 class="text-3xl font-bold mb-8">Bonus credit</h1>
      <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">For every successful referral you will be rewarded with <span class="text-fuchsia-600 font-bold">CHF 100</span> !</h2>
        <div class="mb-4">
          <span class="font-semibold">Share your unique referral link now:</span>
          <div class="flex items-center mt-2">
            <input
              ref="referralInput"
              type="text"
              class="w-full px-4 py-2 rounded-l bg-gray-900 text-white font-mono text-sm"
              :value="referralLink"
              readonly
            />
            <button @click="copyLink" class="bg-yellow-300 hover:bg-yellow-400 text-gray-900 px-4 py-2 rounded-r font-semibold focus:outline-none">
              <svg v-if="!copied" class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16h8M8 12h8m-7 8h6a2 2 0 002-2V7a2 2 0 00-2-2h-6a2 2 0 00-2 2v2"/></svg>
              <span v-if="!copied">Copy link</span>
              <span v-else>Copied!</span>
            </button>
          </div>
        </div>
      </div>
      <div class="bg-gray-100 rounded-lg p-6">
        <h3 class="text-xl font-bold mb-4">How it works</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-700">
          <li>Share your referral link with other clubs, organizations or schools.</li>
          <li>Anyone who uses your referral link when registering will receive a CHF 50 discount on their first annual fundoo license.</li>
          <li>As a reward, you will receive a credit of CHF 100.*</li>
        </ol>
        <p class="text-xs text-gray-500 mt-4">*The amount will be credited to your next project invoice, provided your contact has completed and invoiced a project with fundoo.</p>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const page = usePage();
const referralLink = ref(page.props.auth?.user?.referral_link || 'https://app.fundoo.ch/de/users/register?registration_discount_code=C3BE7A');
const copied = ref(false);

function copyLink() {
  navigator.clipboard.writeText(referralLink.value);
  copied.value = true;
  setTimeout(() => (copied.value = false), 1500);
}
</script>

<style scoped>
.text-fuchsia-600 {
  color: #d946ef;
}
</style> 