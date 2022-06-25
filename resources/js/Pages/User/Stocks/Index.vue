<template>
    <table-layout
        title="Products/BRANCH"
        note="You can click edit icon to edit the reordering point and kg per day, click the column header to sort the table."
    >
        <Head title="Products-BRANCH" />
        <JetValidationErrors class="mb-4" />
        <div
            class="flex sm:flex-row flex-col gap-2 justify-between items-center p-4 bg-white dark:bg-gray-800 sm:rounded-t-lg"
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
                        v-model="user_product_params.search"
                        type="text"
                        id="table-search"
                        class="bg-gray-50 sm:w-80 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search for products"
                    />
                </div>
                <div>
                    <select
                        v-model="user_product_params.product_type"
                        class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option selected :value="null">Choose type</option>
                        <option value="chicken">Chicken</option>
                        <option value="pork">Pork</option>
                        <option value="beef">Beef</option>
                    </select>
                </div>
            </div>
            <div class="mr-4 flex gap-1 items-center">
                <a
                    class="flex items-center"
                    :href="route('stocks.export.pdf', user_product_params)"
                >
                    <document-download-icon class="w-6 h-6 text-red-500" />
                    <span class="text-xs text-gray-400">PDF</span>
                </a>
                <a
                    class="flex items-center"
                    :href="route('stocks.export.excel', user_product_params)"
                >
                    <document-download-icon class="w-6 h-6 text-green-500" />
                    <span class="text-xs text-gray-400 uppercase">Excel</span>
                </a>
            </div>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-b-lg">
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('name')"
                                >Product name
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            user_product_params.field ===
                                                'name' &&
                                            user_product_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            user_product_params.field ===
                                                'name' &&
                                            user_product_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('type')"
                                >Type
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            user_product_params.field ===
                                                'type' &&
                                            user_product_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            user_product_params.field ===
                                                'type' &&
                                            user_product_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('price')"
                                >Price
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            user_product_params.field ===
                                                'price' &&
                                            user_product_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            user_product_params.field ===
                                                'price' &&
                                            user_product_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('quantity')"
                                >Stocks
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            user_product_params.field ===
                                                'quantity' &&
                                            user_product_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            user_product_params.field ===
                                                'quantity' &&
                                            user_product_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3">ROP</th>
                        <th scope="col" class="px-6 py-3">
                            Maximum <br />
                            Shelf Life
                        </th>
                        <th scope="col" class="px-6 py-3">
                            KG per <br />
                            DAY
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <table-row-stocks
                        v-for="product in stocks.data"
                        :key="product.id"
                        :stock="product"
                    >
                    </table-row-stocks>
                </tbody>
            </table>
        </div>
        <pagination :links="stocks.links" />
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Link, Head } from "@inertiajs/inertia-vue3";
import Tooltip from "@/Components/Tooltip.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import Pagination from "@/Components/Pagination.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import TableRowStocks from "./TableRowStocks.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import {
    PlusIcon,
    DocumentTextIcon,
    PencilIcon,
    TrashIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    DocumentDownloadIcon,
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
        JetButton,
        JetConfirmationModal,
        Pagination,
        SortAscendingIcon,
        SortDescendingIcon,
        TableRowStocks,
        JetValidationErrors,
        DocumentDownloadIcon,
    },
    props: {
        stocks: {
            type: Object,
            default: () => {},
        },
        stocks_filter: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            isShow: false,
            productId: null,
            user_product_params: {
                search: this.stocks_filter.search,
                field: this.stocks_filter.field,
                direction: this.stocks_filter.direction,
                product_type: this.stocks_filter.product_type,
            },
        };
    },
    methods: {
        openConfirmation(id) {
            this.productId = id;
            this.isShow = true;
        },

        sort(field) {
            this.user_product_params.field = field;
            this.user_product_params.direction =
                this.user_product_params.direction === "asc" ? "desc" : "asc";
        },
    },
    watch: {
        user_product_params: {
            handler: throttle(function () {
                let user_product_params = pickBy(this.user_product_params);

                this.$inertia.get(
                    this.route("stocks.index"),
                    user_product_params,
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
