<template>
    <div class="m-8">
        <div class="font-light text-3xl text-gray-700 dark:text-gray-300">
            {{ header }}
        </div>
        <div class="font-bold text-gray-700 dark:text-gray-300">
            {{ title }}
        </div>
    </div>
</template>

<script setup>
defineProps({
    header: String,
    title: String,
});
</script>

<style></style>
