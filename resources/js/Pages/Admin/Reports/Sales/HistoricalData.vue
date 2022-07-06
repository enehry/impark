<template>
    <table-layout
        title="Previous Sales Report "
        backRoute="sales-report.index"
        note="You can change the branch, and product type by the dropdown select. 
        Clicking the colum header will sort the data by that column.
         No selected start date or end date will show today sales.
         Start date and end date will show sales between the selected dates."
    >
        <Head title="Sales Report" />
        <div
            class="w-full flex md:flex-row gap-2 flex-col justify-between items-center p-4 bg-white dark:bg-gray-800 sm:rounded-t-lg shadow-md"
        >
            <div class="w-full flex flex-col gap-2">
                <div
                    class="flex flex-col sm:flex-row w-full justify-between items-center gap-2"
                >
                    <div class="flex gap-2 items-center">
                        <Link
                            :href="route('sales-report.chart')"
                            class="text-green-500 cursor-pointer hover:text-green-300 uppercase text-xs flex items-center"
                        >
                            <ChartSquareBarIcon class="w-6 h-6" />
                            <div class="text-gray-500">Chart</div>
                        </Link>
                        <a
                            :href="
                                route(
                                    'sales-report.historical-data-pdf',
                                    params
                                )
                            "
                            class="text-red-500 hover:text-red-300 uppercase text-xs flex items-center"
                        >
                            <DocumentDownloadIcon class="w-6 h-6" />
                            <div class="text-gray-500">PDF</div>
                        </a>
                        <a
                            :href="
                                route(
                                    'sales-report.historical-data-excel',
                                    params
                                )
                            "
                            class="text-green-500 hover:text-green-300 uppercase text-xs flex items-center"
                        >
                            <DocumentDownloadIcon class="w-6 h-6" />
                            <div class="text-gray-500">Excel</div>
                        </a>
                    </div>
                    <div
                        class="flex gap-2 md:flex-row flex-col md:w-min w-full"
                    >
                        <div
                            class="flex items-center justify-between sm:w-min w-full"
                        >
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
                                    v-model="params.start_date"
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
                                    v-model="params.end_date"
                                    name="end"
                                    type="date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Select date end"
                                />
                            </div>
                        </div>
                        <div class="w-full">
                            <label for="table-branch" class="sr-only"
                                >Type</label
                            >
                            <select
                                v-model="params.group_by"
                                class="bg-gray-50 w-full sm:w-min border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                                <option selected :value="null">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- 2nd layer-->
                <div class="flex md:flex-row flex-col gap-2 justify-between">
                    <div class="w-full">
                        <label for="table-search" class="sr-only">Search</label>
                        <div class="relative mt-1 w-full">
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
                                v-model="params.search"
                                type="text"
                                id="table-search"
                                class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search for products"
                            />
                        </div>
                    </div>
                    <div class="flex w-full sm:w-min justify-between gap-2">
                        <div class="mt-1 w-full sm:w-min">
                            <label for="table-branch" class="sr-only"
                                >Branch</label
                            >
                            <select
                                v-model="params.branch_id"
                                class="bg-gray-50 w-full sm:w-min border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                                <option selected :value="null">
                                    Choose branch
                                </option>
                                <option
                                    v-for="branch in sales_history_branches"
                                    :value="branch.id"
                                    :key="branch.id"
                                >
                                    {{ branch.name }}
                                </option>
                            </select>
                        </div>
                        <div class="mt-1 w-full">
                            <label for="table-branch" class="sr-only"
                                >Type</label
                            >
                            <select
                                v-model="params.type"
                                class="bg-gray-50 w-full sm:w-min border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                                <option selected :value="null">
                                    Choose type
                                </option>
                                <option value="pork">Pork</option>
                                <option value="chicken">Chicken</option>
                                <option value="beef">Beef</option>
                            </select>
                        </div>
                    </div>
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
                                            params.field === 'products.name' &&
                                            params.direction === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            params.field === 'products.name' &&
                                            params.direction === 'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3">Type</th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1 items-center"
                                @click.prevent="sort('sold_quantity')"
                            >
                                Sold <br />
                                Quantity(kg)
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            params.field === 'sold_quantity' &&
                                            params.direction === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            params.field === 'sold_quantity' &&
                                            params.direction === 'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('products.price')"
                            >
                                Price
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            params.field === 'products.price' &&
                                            params.direction === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            params.field === 'products.price' &&
                                            params.direction === 'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('total_sales')"
                            >
                                Total Sales
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            params.field === 'total_sales' &&
                                            params.direction === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            params.field === 'total_sales' &&
                                            params.direction === 'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('date')"
                            >
                                Date
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            params.field === 'date' &&
                                            params.direction === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            params.field === 'date' &&
                                            params.direction === 'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <row-historical-data
                        v-for="sale in sales_history.data"
                        :key="sale.id"
                        :sale="sale"
                    >
                    </row-historical-data>
                </tbody>
            </table>
        </div>
        <pagination :links="sales_history.links"></pagination>
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import RowHistoricalData from "./RowHistoricalData.vue";
import {
    DocumentDownloadIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    TableIcon,
    ChartSquareBarIcon,
} from "@heroicons/vue/solid";
import Pagination from "@/Components/Pagination.vue";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
export default {
    components: {
        TableLayout,
        Head,
        DocumentDownloadIcon,
        Pagination,
        SortAscendingIcon,
        SortDescendingIcon,
        TableIcon,
        Link,
        RowHistoricalData,
        ChartSquareBarIcon,
    },
    props: {
        sales_history_branches: {
            type: Array,
            default: () => [],
        },
        sales_history: {
            type: Object,
            default: () => {},
        },
        sales_history_filter: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            params: {
                search: this.sales_history_filter.search,
                branch_id: this.sales_history_filter.branch_id,
                type: this.sales_history_filter.type,
                field: this.sales_history_filter.field,
                direction: this.sales_history_filter.direction,
                start_date: this.sales_history_filter.start_date,
                end_date: this.sales_history_filter.end_date,
                group_by: this.sales_history_filter.group_by,
            },
        };
    },
    methods: {
        sort(field) {
            this.params.field = field;
            this.params.direction =
                this.params.direction === "asc" ? "desc" : "asc";
        },
    },
    computed: {},
    watch: {
        params: {
            handler: throttle(function () {
                let params = pickBy(this.params);

                this.$inertia.get(
                    route("sales-report.historical-data"),
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
