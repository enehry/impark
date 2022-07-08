<template>
    <table-layout
        title="Received Product Logs"
        backRoute="receive-products.index"
        note="You can change the range of received date and select and specific products."
    >
        <Head title="Received Products Logs" />
        <div
            class="flex flex-col gap-2 shadow-md justify-between p-4 bg-white dark:bg-gray-800 md:rounded-t-lg"
        >
            <div class="flex w-full">
                <div class="flex gap-2 items-center justify-center w-full">
                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative w-full">
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
                            v-model="transaction_params.search"
                            type="text"
                            id="table-search"
                            class="bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for products"
                        />
                    </div>
                    <div class="flex gap-2">
                        <select
                            v-model="transaction_params.product_type"
                            class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected :value="null">Choose type</option>
                            <option value="chicken">Chicken</option>
                            <option value="pork">Pork</option>
                            <option value="beef">Beef</option>
                        </select>
                    </div>
                </div>
            </div>
            <div
                class="flex gap-2 sm:flex-row flex-col justify-between items-center"
            >
                <div class="flex items-center justify-between sm:w-min w-full">
                    <div class="relative">
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
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </div>
                        <input
                            v-model="transaction_params.start_date"
                            name="start"
                            type="date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Select date start"
                        />
                    </div>
                    <span class="mx-4 text-xs text-gray-500">to</span>
                    <div class="relative">
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
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </div>
                        <input
                            v-model="transaction_params.end_date"
                            name="end"
                            type="date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Select date end"
                        />
                    </div>
                </div>
                <div class="flex gap-2 items-center">
                    <a
                        :href="
                            route('received-products.pdf', transaction_params)
                        "
                        class="text-red-500 hover:text-red-300 uppercase text-xs flex items-center"
                    >
                        <DocumentDownloadIcon class="w-6 h-6" />
                        <div class="text-gray-500">PDF</div>
                    </a>
                    <a
                        :href="
                            route('received-products.excel', transaction_params)
                        "
                        class="text-green-500 hover:text-green-300 uppercase text-xs flex items-center"
                    >
                        <DocumentDownloadIcon class="w-6 h-6" />
                        <div class="text-gray-500">Excel</div>
                    </a>
                </div>
            </div>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-b-lg">
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('products.name')"
                                >Product name
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'products.name' &&
                                            transaction_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'products.name' &&
                                            transaction_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('products.type')"
                                >Type
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'products.type' &&
                                            transaction_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'products.type' &&
                                            transaction_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="
                                    sort('planned_orders.order_quantity')
                                "
                                >Received(KG)
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'planned_orders.order_quantity' &&
                                            transaction_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'planned_orders.order_quantity' &&
                                            transaction_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('delivered_at')"
                                >Delivered Date
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'delivered_at' &&
                                            transaction_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'delivered_at' &&
                                            transaction_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('received_at')"
                                >Received Date
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'received_at' &&
                                            transaction_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            transaction_params.field ===
                                                'received_at' &&
                                            transaction_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody v-if="hasData">
                    <RowReceivedLogs
                        v-for="product in transactions.data"
                        :key="product.id"
                        :stock="product"
                    />
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="6" class="text-center">
                            <empty label="Receive Products"></empty>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination :links="transactions.links" />
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Link, Head } from "@inertiajs/inertia-vue3";
import Tooltip from "@/Components/Tooltip.vue";
import Pagination from "@/Components/Pagination.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import RowReceivedLogs from "./RowReceivedLogs.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import Empty from "@/Components/Empty.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";

import {
    PlusIcon,
    DocumentTextIcon,
    PencilIcon,
    TrashIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    DocumentDownloadIcon,
    InformationCircleIcon,
} from "@heroicons/vue/solid";
export default {
    components: {
        TableLayout,
        Head,
        Link,
        Tooltip,
        PlusIcon,
        TrashIcon,
        PencilIcon,
        DocumentTextIcon,
        Pagination,
        SortAscendingIcon,
        SortDescendingIcon,
        RowReceivedLogs,
        JetValidationErrors,
        DocumentDownloadIcon,
        Empty,
        JetSecondaryButton,
        InformationCircleIcon,
    },
    props: {
        transactions: {
            type: Object,
            default: () => {},
        },
        transaction_filter: {
            type: Object,
            default: () => {},
        },
        transaction_branches: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            isShow: false,
            productId: null,
            transaction_params: {
                search: this.transaction_filter.search,
                field: this.transaction_filter.field,
                direction: this.transaction_filter.direction,
                product_type: this.transaction_filter.product_type,
                start_date: this.transaction_filter.start_date,
                end_date: this.transaction_filter.end_date,
            },
            isLoading: false,
            message: "",
            error: {
                message: "",
                title: "",
            },
            type: null,
        };
    },
    computed: {
        hasData() {
            return this.transactions.data.length > 0;
        },
    },
    methods: {
        sort(field) {
            this.transaction_params.field = field;
            this.transaction_params.direction =
                this.transaction_params.direction === "asc" ? "desc" : "asc";
        },
    },
    watch: {
        transaction_params: {
            handler: throttle(function () {
                let transaction_params = pickBy(this.transaction_params);
                this.$inertia.get(
                    this.route("receive-products-logs.index"),
                    transaction_params,
                    {
                        preserveState: true,
                        preserveScroll: true,
                        replace: true,
                    }
                );
            }, 100),
            deep: true,
        },
    },
};
</script>

<style></style>
