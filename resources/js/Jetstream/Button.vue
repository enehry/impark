<script setup>
defineProps({
    type: {
        type: String,
        default: "submit",
    },
});
</script>

<template>
    <button
        :type="type"
        class="dark:bg-gray-700 dark:hover:hover:bg-gray-600 dark:active:bg-gray-600 dark:focus:border-gray-600 dark:focus:ring dark:focus:ring-gray-300 inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition"
    >
        <slot />
    </button>
</template>
