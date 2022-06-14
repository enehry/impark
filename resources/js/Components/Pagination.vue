<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap pt-4 pb-8">
            <template v-for="(link, key) in links">
                <div
                    v-if="link.url === null"
                    :key="key"
                    class="mb-1 mr-1 px-4 py-3 text-gray-400 text-sm leading-4 border rounded dark:border-gray-500 hover:bg-gray-700 dark:hover:text-white dark:text-gray-200"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :key="`link-${key}`"
                    class="mb-1 mr-1 px-4 py-3 focus:text-indigo-500 text-sm leading-4 hover:bg-white border focus:border-indigo-500 rounded dark:hover:bg-gray-700 dark:text-gray-200 dark:border-gray-500"
                    :class="{ 'bg-white dark:bg-gray-700': link.active }"
                    :href="link.url"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
export default {
    components: {
        Link,
    },
    props: {
        links: Array,
    },
};
</script>
