<template>
    <div class="w-36 h-36 bg-red-500 rounded-lg p-2 dark:bg-gray-800">
        <Link :href="route(routeName)">
            <div class="flex flex-col justify-between h-full">
                <slot></slot>
                <h1
                    v-if="counter"
                    class="text-white font-bold text-4xl text-right dark:text-gray-400"
                >
                    {{ counter }}
                </h1>
                <h1
                    class="text-white text-md uppercase text-right dark:text-gray-500"
                >
                    {{ label }}
                </h1>
            </div>
        </Link>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
defineProps({
    label: String,
    counter: Number,
    routeName: {
        type: String,
        default: "Menu",
    },
});
</script>

<style></style>
