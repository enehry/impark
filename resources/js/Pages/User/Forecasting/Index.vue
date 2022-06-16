<template>
    <table-layout title="Forecasting/BRANCH">
        <Head title="Forecasting-BRANCH" />
        <JetValidationErrors class="mb-4" />
        <div v-if="forecast_stocks.length > 0">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div
                    class="flex justify-between items-center p-4 bg-white dark:bg-gray-800"
                >
                    <div class="flex gap-2 items-center justify-center">
                        <label for="table-search" class="sr-only">Search</label>
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
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </div>
                            <input
                                v-model="user_forecasting_param.search"
                                type="text"
                                id="table-search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-80 pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search for products"
                            />
                        </div>
                        <div>
                            <select
                                v-model="user_forecasting_param.product_type"
                                class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                                <option selected value="">Choose type</option>
                                <option value="chicken">Chicken</option>
                                <option value="pork">Pork</option>
                                <option value="beef">Beef</option>
                            </select>
                        </div>
                    </div>
                    <div class="mr-4 flex gap-1 items-center"></div>
                </div>
                <table
                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
                >
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                    >
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                <span
                                    class="flex gap-1 items-center cursor-pointer"
                                    @click.prevent="sort('name')"
                                    >Product name
                                    <div class="w-4 h-4 cursor-pointer">
                                        <sort-ascending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'name' &&
                                                user_forecasting_param.order ===
                                                    'asc'
                                            "
                                        />
                                        <sort-descending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'name' &&
                                                user_forecasting_param.order ===
                                                    'desc'
                                            "
                                        />
                                    </div>
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span
                                    class="flex gap-1 items-center cursor-pointer"
                                    @click.prevent="sort('type')"
                                    >Type

                                    <div class="w-4 h-4 cursor-pointer">
                                        <sort-ascending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'type' &&
                                                user_forecasting_param.order ===
                                                    'asc'
                                            "
                                        />
                                        <sort-descending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'type' &&
                                                user_forecasting_param.order ===
                                                    'desc'
                                            "
                                        />
                                    </div>
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span
                                    class="flex gap-1 items-center cursor-pointer"
                                    @click.prevent="sort('quantity')"
                                    >Current <br />
                                    Stocks
                                    <div class="w-4 h-4 cursor-pointer">
                                        <sort-ascending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'quantity' &&
                                                user_forecasting_param.order ===
                                                    'asc'
                                            "
                                        />
                                        <sort-descending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'quantity' &&
                                                user_forecasting_param.order ===
                                                    'desc'
                                            "
                                        />
                                    </div>
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-3">Unit</th>
                            <th scope="col" class="px-6 py-3">ROP</th>
                            <th scope="col" class="px-6 py-3">
                                <span
                                    class="flex gap-1 items-center cursor-pointer"
                                    @click.prevent="sort('kg_per_day')"
                                    >KG/DAY
                                    <div class="w-4 h-4 cursor-pointer">
                                        <sort-ascending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'kg_per_day' &&
                                                user_forecasting_param.order ===
                                                    'asc'
                                            "
                                        />
                                        <sort-descending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'kg_per_day' &&
                                                user_forecasting_param.order ===
                                                    'desc'
                                            "
                                        />
                                    </div>
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span
                                    class="flex gap-1 items-center cursor-pointer"
                                    @click.prevent="sort('forecast_quantity')"
                                    >Estimated <br />
                                    Stocks
                                    <div class="w-4 h-4 cursor-pointer">
                                        <sort-ascending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'forecast_quantity' &&
                                                user_forecasting_param.order ===
                                                    'asc'
                                            "
                                        />
                                        <sort-descending-icon
                                            v-if="
                                                user_forecasting_param.sort ===
                                                    'forecast_quantity' &&
                                                user_forecasting_param.order ===
                                                    'desc'
                                            "
                                        />
                                    </div>
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <table-row-forecasting
                            v-for="forecast_stock in forecast_stocks"
                            :key="forecast_stock.id"
                            :forecast_stock="forecast_stock"
                        >
                        </table-row-forecasting>
                    </tbody>
                </table>
            </div>
            <div class="w-full flex justify-end pt-4 pb-20">
                <jet-button @click.prevent="confirm"> Confirm </jet-button>
            </div>
        </div>
        <div v-else>
            <Empty label="Forecasting"></Empty>
        </div>
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Link, Head } from "@inertiajs/inertia-vue3";
import Tooltip from "@/Components/Tooltip.vue";
import JetButton from "@/Jetstream/Button.vue";
import Pagination from "@/Components/Pagination.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import TableRowForecasting from "./TableRowForecasting.vue";
import Empty from "@/Components/Empty.vue";
import {
    PlusIcon,
    DocumentTextIcon,
    PencilIcon,
    TrashIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    DocumentDownloadIcon,
} from "@heroicons/vue/solid";
import EmptyVue from "@/Components/Empty.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
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
        JetButton,
        Pagination,
        SortAscendingIcon,
        SortDescendingIcon,
        DocumentDownloadIcon,
        TableRowForecasting,
        Empty,
        JetValidationErrors,
    },
    props: {
        forecast_stocks: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            isShow: false,
            productId: null,
            user_forecasting_param: {
                search: null,
                sort: null,
                order: null,
                product_type: "",
            },
        };
    },
    methods: {
        openConfirmation(id) {
            this.productId = id;
            this.isShow = true;
        },

        sort(field) {
            this.user_forecasting_param.sort = field;
            this.user_forecasting_param.order =
                this.user_forecasting_param.order === "asc" ? "desc" : "asc";
        },
        confirm() {
            this.$inertia.visit(route("forecast.confirm"), {
                method: "post",
                data: { forecastStocks: this.forecast_stocks },
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    this.isShowModal = true;
                },
            });
        },
    },
    watch: {
        user_forecasting_param: {
            handler: throttle(function () {
                let user_forecasting_param = pickBy(
                    this.user_forecasting_param
                );

                this.$inertia.get(
                    this.route("forecasting.index"),
                    user_forecasting_param,
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
