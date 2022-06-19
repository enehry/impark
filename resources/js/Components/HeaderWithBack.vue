<template>
    <div class="mt-6 md:mx-0 mx-4">
        <Link
            :href="route(routeName)"
            class="flex gap-2 items-center w-min font-medium text-gray-700 dark:text-gray-300"
        >
            <arrow-left-icon class="w-4 h-4" /> Back
        </Link>
        <div class="my-8">
            <div class="font-bold text-gray-700 dark:text-gray-300 uppercase">
                {{ title }}
            </div>
            <div v-if="note" class="text-gray-500 text-xs dark:text-gray-300">
                <span class="text-amber-500 uppercase">Note:</span> {{ note }}
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
    note: String,
});
</script>

<style></style>
