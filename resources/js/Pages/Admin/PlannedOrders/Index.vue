<template>
    <table-layout
        title="Planned Orders"
        note="Planned orders are orders came from the branch forecasting, 
        you can trash/archived the planned order and restore it to the trashed page."
    >
        <Head title="Planned Orders" />
        <tooltip
            id="tooltip-archived-orders"
            label="Archived planned orders"
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
                            v-model="planned_orders_params.search"
                            type="text"
                            id="table-search-planned-orders"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-80 pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for products"
                        />
                    </div>
                </div>
                <div class="mt-1">
                    <label for="table-branch" class="sr-only">Branch</label>
                    <select
                        v-model="planned_orders_params.branch"
                        class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option selected :value="null">Choose branch</option>
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
            <div class="flex justify-end gap-2">
                <Link :href="route('planned-orders.all-trashed')">
                    <button
                        data-tooltip-target="tooltip-archived-orders"
                        class="bg-red-500 text-white p-2 rounded-full"
                    >
                        <trash-icon class="w-4 h-4" />
                    </button>
                </Link>
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
                        <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input
                                    @change="checkAll($event)"
                                    id="checkbox-all"
                                    type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                />
                                <label for="checkbox-all" class="sr-only"
                                    >checkbox</label
                                >
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">Branch</th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-2 items-center"
                                @click.prevent="sort('products.name')"
                                >Product name
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            planned_orders_params.field ===
                                                'products.name' &&
                                            planned_orders_params.order ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            planned_orders_params.field ===
                                                'products.name' &&
                                            planned_orders_params.order ===
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
                                            planned_orders_params.field ===
                                                'planned_orders.order_quantity' &&
                                            planned_orders_params.order ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            planned_orders_params.field ===
                                                'planned_orders.order_quantity' &&
                                            planned_orders_params.order ===
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
                    <table-row-planned-orders
                        v-for="planned_order in planned_orders_admin"
                        :key="planned_order.id"
                        :planned_order="planned_order"
                        :checkboxChange="onCheckboxChange"
                        :isCheckedAll="isCheckedAll"
                    >
                    </table-row-planned-orders>
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
        <div
            v-if="hasData"
            class="pb-20 pt-8 flex justify-between items center"
        >
            <jet-button @click="onCancelAll"> Cancel All </jet-button>
            <div class="flex gap-4">
                <jet-button @click="onConvert"> Convert Selected </jet-button>
                <jet-button @click="onConvertAll"> Convert All </jet-button>
            </div>
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
} from "@heroicons/vue/outline";
import Tooltip from "@/Components/Tooltip.vue";
import TableRowPlannedOrders from "./TableRowPlannedOrders.vue";
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
        TableRowPlannedOrders,
        JetButton,
        Empty,
        SortAscendingIcon,
        SortDescendingIcon,
    },
    name: "PlannedOrdersIndex",
    props: {
        planned_orders_admin: {
            type: Array,
            default: () => [],
        },
        po_branches: {
            type: Array,
            default: () => [],
        },
        po_filters: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            selected_planned_orders: [],
            isCheckedAll: false,
            planned_orders_params: {
                search: this.po_filters.search,
                field: this.po_filters.field,
                order: this.po_filters.order,
                type: this.po_filters.type,
                branch: this.po_filters.branch,
            },
        };
    },
    computed: {
        hasData() {
            return this.planned_orders_admin.length > 0;
        },
    },
    methods: {
        onCheckboxChange(id, checked) {
            if (checked) {
                this.selected_planned_orders.push(id);
            } else {
                this.selected_planned_orders.splice(
                    this.selected_planned_orders.indexOf(id),
                    1
                );
            }
            console.log(this.selected_planned_orders);
        },
        checkAll($event) {
            if ($event.target.checked) {
                this.selected_planned_orders = this.planned_orders_admin.map(
                    (planned_order) => planned_order.id
                );
                this.isCheckedAll = true;
            } else {
                this.selected_planned_orders = [];
                this.isCheckedAll = false;
            }
            console.log(this.selected_planned_orders);
        },
        onConvertAll() {
            this.$inertia.visit(route("planned-orders.convert"), {
                method: "POST",
                data: {
                    ids: this.planned_orders_admin.map(
                        (planned_order) => planned_order.id
                    ),
                },
                onBefore: (visit) => {},
                onSuccess: (response) => {},
            });
        },
        onConvert() {
            this.$inertia.visit(route("planned-orders.convert"), {
                method: "POST",
                data: {
                    ids: this.selected_planned_orders,
                },
                preserveState: true,
                preserveScroll: true,
                onBefore: (visit) => {},
                onSuccess: (response) => {},
            });
        },
        onCancelAll() {
            this.$inertia.visit(route("planned-orders.cancel-all"), {
                method: "POST",
                data: {
                    ids: this.planned_orders_admin.map(
                        (planned_order) => planned_order.id
                    ),
                },
                onBefore: (visit) => {},
                onSuccess: (response) => {},
            });
        },
        sort(field) {
            this.planned_orders_params.field = field;
            this.planned_orders_params.order =
                this.planned_orders_params.order === "asc" ? "desc" : "asc";
        },
    },
    watch: {
        planned_orders_params: {
            handler: throttle(function () {
                let params = pickBy(this.planned_orders_params);

                this.$inertia.get(this.route("planned-orders.index"), params, {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                });
            }, 300),
            deep: true,
        },
    },
};
</script>

<style></style>
