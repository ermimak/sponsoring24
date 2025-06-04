<template>
  <a
    :href="typeof to === 'string' ? to : '#'"
    class="flex items-center px-4 py-2 rounded transition-colors font-medium hover:bg-primary-dark hover:text-accent"
    :class="{ 'bg-primary-dark text-accent': isActive }"
    @click.prevent="navigate"
  >
    <i v-if="icon" :class="iconClass" class="mr-3"></i>
    <span>{{ label }}</span>
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
    'currency-dollar': 'fas fa-coins'
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