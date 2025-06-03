<template>
  <input
    type="checkbox"
    :checked="modelValue"
    @change="$emit('update:modelValue', $event.target.checked)"
    class="rounded border-gray-300 text-primary focus:ring-primary"
    :class="[
      error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '',
      disabled ? 'bg-gray-100 cursor-not-allowed' : '',
      checkboxClass
    ]"
    :disabled="disabled"
    v-bind="$attrs"
  />
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  error: {
    type: [String, Boolean],
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  checkboxClass: {
    type: String,
    default: '',
  },
});

defineEmits(['update:modelValue']);
</script>
