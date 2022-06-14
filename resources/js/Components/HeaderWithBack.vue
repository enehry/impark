<template>
    <div class="mt-6">
        <Link
            :href="route(routeName)"
            class="flex gap-2 items-center font-medium text-gray-700 dark:text-gray-300"
        >
            <arrow-left-icon class="w-4 h-4" /> Back
        </Link>
        <div class="my-8">
            <div class="font-bold text-gray-700 dark:text-gray-300 uppercase">
                {{ title }}
            </div>
        </div>
    </div>
</template>

<script setup>
// add hero icons
import { ArrowLeftIcon } from "@heroicons/vue/outline";
import { Link } from "@inertiajs/inertia-vue3";

defineProps({
    title: String,
    routeName: {
        type: String,
        default: "Menu",
    },
});
</script>

<style></style>
