<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Jetstream/Welcome.vue";
import MenuTile from "../Components/MenuTile.vue";
import {
    CubeIcon,
    TagIcon,
    ClipboardListIcon,
    TruckIcon,
    ShareIcon,
    UsersIcon,
    DocumentReportIcon,
} from "@heroicons/vue/outline";
import MenuLayout from "../Layouts/MenuLayout.vue";
defineProps({
    product_count: {
        type: Number,
        default: 0,
    },
});
</script>

<template>
    <MenuLayout header="WELCOME ADMIN!" title="MENU/WAREHOUSE">
        <div
            class="m-auto grid md:grid-cols-3 lg:gap-20 gap-10 grid-cols-2 content-evenly"
        >
            <MenuTile
                :counter="product_count"
                label="products"
                routeName="products.index"
            >
                <CubeIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile label="Bill of Materials" routeName="bom.index">
                <TagIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile :counter="4" label="Planned Orders">
                <ClipboardListIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
            <MenuTile label="Distribute Receivables">
                <TruckIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>

            <MenuTile :counter="3" label="Branches" routeName="branch.index">
                <ShareIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile :counter="4" label="Users" routeName="users.index">
                <UsersIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile label="Reports">
                <DocumentReportIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
        </div>
    </MenuLayout>
</template>
