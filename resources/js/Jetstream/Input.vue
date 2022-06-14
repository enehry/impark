<script setup>
import { onMounted, ref } from "vue";

defineProps({
    modelValue: String,
    hasError: Boolean,
});

defineEmits(["update:modelValue"]);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:bg-gray-600 dark:text-gray-300"
        :class="{
            'border-red-500': hasError,
            'border-gray-300': !hasError,
        }"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
