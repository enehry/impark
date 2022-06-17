<template>
    <div class="flex justify-center items-center gap-2 py-8">
        <information-circle-icon
            class="w-6 h-6 text-gray-500 dark:text-gray-400"
        ></information-circle-icon>
        <h1
            class="uppercase font-bold text-sm text-gray-500 dark:text-gray-400"
        >
            {{ label }} is empty
        </h1>
    </div>
</template>

<script setup>
import { InformationCircleIcon } from "@heroicons/vue/outline";
defineProps({
    label: String,
});
</script>

<style></style>
