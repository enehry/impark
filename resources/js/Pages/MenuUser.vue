<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Jetstream/Welcome.vue";
import MenuTile from "../Components/MenuTile.vue";
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
    <AppLayout title="Menu">
        <div class="mx-auto max-w-2xl">
            <div class="m-8">
                <div
                    class="font-light text-3xl text-gray-700 dark:text-gray-300"
                >
                    WELCOME!
                </div>
                <div class="font-bold text-gray-700 dark:text-gray-300">
                    MENU/BRANCH
                </div>
            </div>
            <div class="flex justify-center pb-28">
                <div
                    class="m-auto grid md:grid-cols-3 lg:gap-20 gap-10 grid-cols-2 content-evenly"
                >
                    <MenuTile label="Issue Products">
                        <CubeIcon
                            class="w-12 h-12 text-white dark:text-gray-500"
                        />
                    </MenuTile>
                    <MenuTile label="Inventory">
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
                        <TruckIcon
                            class="w-12 h-12 text-white dark:text-gray-500"
                        />
                    </MenuTile>
                    <MenuTile label="Reports">
                        <DocumentReportIcon
                            class="w-12 h-12 text-white dark:text-gray-500"
                        />
                    </MenuTile>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
