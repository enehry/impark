<template>
    <li class="mb-10 ml-6">
        <span
            class="flex absolute -left-3 justify-center items-center shadow-lg w-7 h-7 bg-blue-200 rounded-full ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900"
        >
            <img
                class="rounded-full dark:text-gray-600"
                :src="log.user.profile_photo_url"
                :alt="log.user.name"
            />
        </span>
        <div
            class="justify-between items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:flex dark:bg-gray-700 dark:border-gray-600"
        >
            <time
                class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0"
                >{{ formattedDate }}</time
            >
            <div
                class="text-sm font-normal text-gray-500 lex dark:text-gray-300"
            >
                <span
                    class="text-gray-700 font-medium dark:text-gray-500 uppercase"
                >
                    {{ log.action }}
                </span>
                <span class="ml-2">{{ log.description }}</span>
            </div>
        </div>
    </li>
</template>

<script>
import moment from "moment";
export default {
    props: {
        log: {
            type: Object,
            default: () => {},
        },
    },
    computed: {
        formattedDate() {
            return moment(this.log.created_at).fromNow();
        },
    },
};
</script>

<style></style>
