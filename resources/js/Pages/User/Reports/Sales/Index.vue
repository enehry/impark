<template>
    <table-layout
        :title="`Sales Report ${today}`"
        backRoute="report-branch.index"
        note="You can change the branch, and product type by the dropdown select. Clicking the colum header will sort the data by that column."
    >
        <Head :title="`Sales Report ${today}`" />
        <div
            class="w-full flex md:flex-row gap-2 flex-col justify-between items-center p-4 bg-white dark:bg-gray-800 sm:rounded-t-lg shadow-md"
        >
            <div class="flex md:flex-row flex-col gap-2">
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
                            v-model="params.search"
                            type="text"
                            id="table-search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-60 pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for products"
                        />
                    </div>
                </div>
                <div class="flex gap-2">
                    <div class="mt-1">
                        <label for="table-branch" class="sr-only">Type</label>
                        <select
                            v-model="params.type"
                            class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected :value="null">Choose type</option>
                            <option value="pork">Pork</option>
                            <option value="chicken">Chicken</option>
                            <option value="beef">Beef</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 items-center">
                <Link
                    :href="route('sales-report-branch.historical-index')"
                    class="text-green-500 cursor-pointer hover:text-green-300 uppercase text-xs flex items-center"
                >
                    <TableIcon class="w-6 h-6" />
                    <div class="text-gray-500">All Sales Data</div>
                </Link>
                <a
                    :href="
                        hasData
                            ? route('sales-report-branch.pdf', params)
                            : '/#'
                    "
                    class="text-red-500 hover:text-red-300 uppercase text-xs flex items-center"
                >
                    <DocumentDownloadIcon class="w-6 h-6" />
                    <div class="text-gray-500">PDF</div>
                </a>
                <a
                    :href="
                        hasData
                            ? route('sales-report-branch.excel', params)
                            : '/#'
                    "
                    class="text-green-500 hover:text-green-300 uppercase text-xs flex items-center"
                >
                    <DocumentDownloadIcon class="w-6 h-6" />
                    <div class="text-gray-500">Excel</div>
                </a>
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
                                Quantity
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
                    </tr>
                </thead>
                <tbody v-if="hasData">
                    <tr
                        v-for="sale in sales.data"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
                        >
                            {{ sale.name }}
                        </th>
                        <td class="px-6 py-4">{{ sale.type }}</td>
                        <td class="px-6 py-4">{{ sale.sold_quantity }}</td>
                        <td class="px-6 py-4">{{ sale.price }}</td>
                        <td class="px-6 py-4">{{ sale.total_sales }}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="5" class="px-6 py-4">
                            <Empty label="Today sales report" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination :links="sales.links"></pagination>
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Empty from "@/Components/Empty.vue";
import {
    DocumentDownloadIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    TableIcon,
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
        Empty,
    },
    props: {
        sales: {
            type: Object,
            default: () => {},
        },
        sales_filter: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            params: {
                search: this.sales_filter.search,
                type: this.sales_filter.type,
                field: this.sales_filter.field,
                direction: this.sales_filter.order,
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
    computed: {
        // get date mmm dd, yyyy
        today() {
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, "0");
            const mm = today.toLocaleString("default", {
                month: "short",
            });
            const yyyy = today.getFullYear();
            return `${mm} ${dd}, ${yyyy}`;
        },
        hasData() {
            return this.sales.data.length > 0;
        },
    },
    watch: {
        params: {
            handler: throttle(function () {
                let params = pickBy(this.params);

                this.$inertia.get(route("sales-report-branch.index"), params, {
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
