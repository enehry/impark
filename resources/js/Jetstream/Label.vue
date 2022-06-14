<script setup>
defineProps({
    value: String,
});
</script>

<template>
    <label
        class="block font-medium text-sm text-gray-700 mb-2 ml-2 dark:text-gray-400"
    >
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
