<template>
  <div v-if="visible" class="fixed bottom-6 right-6 z-50 bg-[#18182a] text-white rounded-xl shadow-2xl p-6 w-[380px] max-w-full flex flex-col gap-4 animate-fade-in">
    <div class="font-bold text-lg mb-1">We use cookies!</div>
    <div class="text-sm text-white/80 mb-2">
      We use cookies to analyze our website traffic and improve your user experience. For detailed information and individual settings, click
      <a href="#" class="underline text-purple-300 hover:text-purple-200">here</a>.
    </div>
    <div class="flex gap-3 justify-end">
      <button @click="acceptAll" class="bg-purple-600 hover:bg-purple-700 text-white font-bold px-5 py-2 rounded-lg shadow transition-colors">Accept all</button>
      <button @click="acceptNecessary" class="text-white/70 hover:text-white underline text-sm">Only necessary cookies</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
const visible = ref(true);

function acceptAll() {
  visible.value = false;
  // TODO: Set cookie consent state
}
function acceptNecessary() {
  visible.value = false;
  // TODO: Set cookie consent state (necessary only)
}
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fade-in 0.5s;
}
</style> 