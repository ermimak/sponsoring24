<template>
  <a
    :href="typeof to === 'string' ? to : '#'"
    class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 font-medium text-blue-100 hover:bg-white/10 group relative overflow-hidden"
    :class="{ 'bg-gradient-to-r from-purple-600/80 to-blue-600/80 text-white shadow-md': isActive }"
    @click.prevent="navigate"
  >
    <div v-if="isActive" class="absolute inset-0 bg-white/5 rounded-lg"></div>
    <div v-if="icon" class="flex items-center justify-center w-8 h-8 mr-3 rounded-md bg-white/10 group-hover:bg-white/20 transition-colors duration-200 relative">
      <i :class="iconClass" class="text-blue-200 group-hover:text-white transition-colors duration-200"></i>
    </div>
    <span class="relative">{{ label }}</span>
    <div v-if="isActive" class="absolute right-3 w-1.5 h-8 bg-white rounded-full"></div>
  </a>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const props = defineProps({
  to: { type: [String, Object], required: true },
  label: { type: String, required: true },
  icon: { type: String, default: '' }
})

const page = usePage()
const isActive = computed(() => {
  if (typeof props.to === 'string') {
    return page.url.startsWith(props.to)
  }
  // For route objects, we can't easily determine if it's active
  // This is a simplification
  return false
})

const iconClass = computed(() => {
  const map = {
    users: 'fas fa-users',
    folder: 'fas fa-folder',
    adjustments: 'fas fa-sliders-h',
    'lock-closed': 'fas fa-lock',
    'currency-dollar': 'fas fa-coins',
    'chart-bar': 'fas fa-chart-bar',
    'referral': 'fas fa-person',
    'license': 'fas fa-calendar-check'
  }
  return map[props.icon] || props.icon
})

function navigate() {
  if (typeof props.to === 'string') {
    router.visit(props.to)
  } else {
    // Assume it's a route object from Ziggy
    try {
      router.visit(props.to)
    } catch (error) {
      console.error('Navigation error:', error)
      // Fallback to direct navigation if router.visit fails
      window.location.href = '/dashboard/members'
    }
  }
}
</script>