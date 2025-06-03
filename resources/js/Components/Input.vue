<template>
  <input
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    class="border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
    :class="[
      error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '',
      disabled ? 'bg-gray-100 cursor-not-allowed' : '',
      inputClass
    ]"
    :disabled="disabled"
    ref="input"
    v-bind="$attrs"
  />
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  error: {
    type: [String, Boolean],
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  inputClass: {
    type: String,
    default: '',
  },
  autofocus: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
  if (props.autofocus) {
    input.value?.focus();
  }
});

watch(() => props.autofocus, (value) => {
  if (value) {
    input.value?.focus();
  }
});
</script>
