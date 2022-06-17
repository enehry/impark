<template>
    <Head title="Planned Orders" />
    <table-layout title="Planned Orders" backRoute="planned-orders.index">
        <tooltip
            id="tooltip-archived-restore"
            label="Restore archived planned orders"
        ></tooltip>
        <div
            class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 rounded-t-lg shadow-md"
        >
            <div class="block sm:flex items-center gap-4">
                <div>
                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative mt-1">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <svg
                                class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </div>

                        <input
                            v-model="order_trashed_params.search"
                            type="text"
                            id="table-search-planned-restore"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-80 pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for products"
                        />
                    </div>
                </div>
                <div class="mt-1">
                    <label for="table-search" class="sr-only">Search</label>
                    <select
                        v-model="order_trashed_params.branch"
                        class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option selected value="">Choose branch</option>
                        <option
                            v-for="branch in po_branches"
                            :value="branch.id"
                            :key="branch.id"
                        >
                            {{ branch.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <jet-button
                    @click="onRestoreAll"
                    data-tooltip-target="tooltip-archived-restore"
                    class="bg-red-500 text-white flex gap-2"
                >
                    <refresh-icon class="w-4 h-4" />
                    <div>Restore all</div>
                </jet-button>
            </div>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-b-lg w-full">
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">Branch</th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-2 items-center"
                                @click.prevent="sort('products.name')"
                                >Product name
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            order_trashed_params.field ===
                                                'products.name' &&
                                            order_trashed_params.order === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            order_trashed_params.field ===
                                                'products.name' &&
                                            order_trashed_params.order ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3">Unit</th>

                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-2 items-center"
                                @click.prevent="
                                    sort('planned_orders.order_quantity')
                                "
                                >Requested <br />
                                Stocks
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            order_trashed_params.field ===
                                                'planned_orders.order_quantity' &&
                                            order_trashed_params.order === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            order_trashed_params.field ===
                                                'planned_orders.order_quantity' &&
                                            order_trashed_params.order ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3">Date</th>
                        <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Edit</span>
                            <span class="sr-only">Delete</span>
                        </th>
                    </tr>
                </thead>
                <tbody v-if="hasData">
                    <table-row-trashed
                        v-for="planned_order in planned_orders_admin_trash"
                        :key="planned_order.id"
                        :planned_order="planned_order"
                    >
                    </table-row-trashed>
                </tbody>
                <tbody v-else>
                    <tr class="text-center">
                        <td colspan="7">
                            <Empty label="Planned orders"> </Empty>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import {
    SortAscendingIcon,
    SortDescendingIcon,
    TrashIcon,
    RefreshIcon,
} from "@heroicons/vue/outline";
import Tooltip from "@/Components/Tooltip.vue";
import TableRowTrashed from "./TableRowTrashed.vue";
import JetButton from "@/Jetstream/Button.vue";
import Empty from "@/Components/Empty.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
export default {
    components: {
        TableLayout,
        Head,
        TrashIcon,
        Link,
        Tooltip,
        TableRowTrashed,
        JetButton,
        Empty,
        SortAscendingIcon,
        SortDescendingIcon,
        RefreshIcon,
    },
    name: "PlannedOrdersIndex",
    props: {
        planned_orders_admin_trash: {
            type: Array,
            default: () => [],
        },
        po_branches: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            selected_planned_orders: [],
            isCheckedAll: false,
            order_trashed_params: {
                search: null,
                field: null,
                order: null,
                type: "",
                branch: "",
            },
        };
    },
    computed: {
        hasData() {
            return this.planned_orders_admin_trash.length > 0;
        },
    },
    methods: {
        onRestoreAll() {
            this.$inertia.visit(route("planned-orders.restore-all"), {
                method: "POST",
                data: {
                    ids: this.planned_orders_admin_trash.map(
                        (planned_order) => planned_order.id
                    ),
                },
                onBefore: (visit) => {},
                onSuccess: (response) => {},
            });
        },
        sort(field) {
            this.order_trashed_params.field = field;
            this.order_trashed_params.order =
                this.order_trashed_params.order === "asc" ? "desc" : "asc";
        },
    },
    watch: {
        order_trashed_params: {
            handler: throttle(function () {
                let params = pickBy(this.order_trashed_params);

                this.$inertia.get(
                    this.route("planned-orders.all-trashed"),
                    params,
                    {
                        preserveState: true,
                        replace: true,
                        preserveScroll: true,
                    }
                );
            }, 300),
            deep: true,
        },
    },
};
</script>

<style></style>
