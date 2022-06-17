<template>
    <div class="absolute" v-if="show && message">
        <div
            id="toast-success"
            class="fixed z-50 flex shadow-xl items-center max-w-xs p-4 space-x-4 text-gray-500 bg-white rounded-lg top-20 right-5 dark:text-gray-400 dark:divide-gray-700 space-x dark:bg-gray-800"
            role="alert"
        >
            <div
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg"
                :class="{
                    'bg-green-500 dark:bg-green-800 dark:text-green-200':
                        style == 'success',
                    'bg-red-500 dark:bg-red-800 dark:text-red-200':
                        style == 'danger',
                }"
            >
                <CheckIcon v-if="style == 'success'" class="w-5 h-5" />
                <ExclamationCircleIcon
                    v-if="style == 'danger'"
                    class="w-5 h-5"
                />
            </div>
            <div class="">{{ message }}</div>
            <button
                type="button"
                @click.prevent="show = false"
                class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                data-dismiss-target="#toast-success"
                aria-label="Close"
            >
                <span class="sr-only">Close</span>
                <svg
                    class="w-5 h-5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    ></path>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { CheckIcon, ExclamationCircleIcon } from "@heroicons/vue/outline";

const show = ref(true);
const style = computed(
    () => usePage().props.value.jetstream.flash?.bannerStyle || "success"
);
const message = computed(
    () => usePage().props.value.jetstream.flash?.banner || ""
);

watch(message, async () => {
    show.value = true;
    await new Promise((resolve) => setTimeout(resolve, 3000));
    usePage().props.value.jetstream.flash = {};
});
</script>

<style></style>
