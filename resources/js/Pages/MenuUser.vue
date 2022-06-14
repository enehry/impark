<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Jetstream/Welcome.vue";
import MenuTile from "@/Components/MenuTile.vue";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import { Head } from "@inertiajs/inertia-vue3";
import {
    CubeIcon,
    DocumentTextIcon,
    ClipboardListIcon,
    TruckIcon,
    ShareIcon,
    UsersIcon,
    DocumentReportIcon,
} from "@heroicons/vue/outline";
</script>

<template>
    <MenuLayout header="WELCOME!" title="MENU/BRANCH">
        <Head title="Menu/Branch" />
        <div
            class="m-auto grid md:grid-cols-3 lg:gap-20 gap-10 grid-cols-2 content-evenly"
        >
            <MenuTile label="Issue Products" routeName="issue-products.index">
                <CubeIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile label="Products" routeName="stocks.index">
                <DocumentTextIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
            <MenuTile label="Generate Forecast">
                <ClipboardListIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
            <MenuTile label="Receive Products">
                <TruckIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile label="Reports">
                <DocumentReportIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
        </div>
    </MenuLayout>
</template>
