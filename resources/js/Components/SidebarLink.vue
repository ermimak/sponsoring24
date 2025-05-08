<template>
  <a
    :href="to"
    class="flex items-center px-4 py-2 rounded transition-colors font-medium hover:bg-primary-dark hover:text-accent"
    :class="{ 'bg-primary-dark text-accent': isActive }"
  >
    <i v-if="icon" :class="iconClass" class="mr-3"></i>
    <span>{{ label }}</span>
  </a>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  to: { type: String, required: true },
  label: { type: String, required: true },
  icon: { type: String, default: '' }
})

const page = usePage()
const isActive = computed(() => page.url.startsWith(props.to))
const iconClass = computed(() => {
  const map = {
    users: 'fas fa-users',
    folder: 'fas fa-folder',
    adjustments: 'fas fa-sliders-h',
    'lock-closed': 'fas fa-lock',
    'currency-dollar': 'fas fa-coins'
  }
  return map[props.icon] || ''
})
</script> 