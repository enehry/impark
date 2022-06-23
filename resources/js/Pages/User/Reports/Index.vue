<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Jetstream/Welcome.vue";
import MenuTile from "@/Components/MenuTile.vue";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import { Head } from "@inertiajs/inertia-vue3";
import {
    CubeIcon,
    DocumentTextIcon,
    DocumentReportIcon,
} from "@heroicons/vue/outline";
defineProps({
    age_counter: {
        type: Number,
        default: 0,
    },
});
</script>

<template>
    <MenuLayout title="Reports" :hasBackBtn="true">
        <Head title="Reports Warehouse" />
        <div
            class="m-auto grid md:grid-cols-3 lg:gap-20 gap-10 grid-cols-2 content-evenly"
        >
            <MenuTile
                label="Inventory Reports"
                routeName="inventory-report-branch.index"
            >
                <CubeIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile label="Sales Reports" routeName="sales-report.index">
                <DocumentReportIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
            <MenuTile
                label="Age Reports"
                routeName="age-report.index"
                :counter="age_counter"
            >
                <DocumentTextIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
        </div>
    </MenuLayout>
</template>
