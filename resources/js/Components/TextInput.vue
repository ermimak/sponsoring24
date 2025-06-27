<template>
    <input
        class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>

<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

defineExpose({ focus: () => input.value.focus() });
</script>
