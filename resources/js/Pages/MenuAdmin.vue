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
    DocumentTextIcon,
    SwitchHorizontalIcon,
} from "@heroicons/vue/outline";
import MenuLayout from "../Layouts/MenuLayout.vue";
defineProps({
    product_count: {
        type: Number,
        default: 0,
    },
    orders_count: {
        type: Number,
        default: 0,
    },
    branch_count: {
        type: Number,
        default: 0,
    },
    user_count: {
        type: Number,
        default: 0,
    },
    issued_orders_count: {
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
            <MenuTile label="Update Price" routeName="bom.index">
                <TagIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile
                :counter="orders_count"
                label="Planned Orders"
                routeName="planned-orders.index"
            >
                <ClipboardListIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
            <MenuTile
                label="Issue Orders"
                :counter="issued_orders_count"
                routeName="issue-orders.index"
            >
                <TruckIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile
                label="Distribute Receivables"
                routeName="distribute-receivables.index"
            >
                <TruckIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile label="Transactions" routeName="transactions.index">
                <SwitchHorizontalIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
            <MenuTile
                label="Branches"
                routeName="branch.index"
                :counter="branch_count"
            >
                <ShareIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile
                :counter="user_count"
                label="Users"
                routeName="users.index"
            >
                <UsersIcon class="w-12 h-12 text-white dark:text-gray-500" />
            </MenuTile>
            <MenuTile label="Reports" routeName="reports.index">
                <DocumentReportIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
            <MenuTile label="Activity logs" routeName="user-logs.index">
                <DocumentTextIcon
                    class="w-12 h-12 text-white dark:text-gray-500"
                />
            </MenuTile>
        </div>
    </MenuLayout>
</template>
