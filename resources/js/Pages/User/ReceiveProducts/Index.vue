<template>
    <table-layout title="RECEIVE PRODUCTS/BRANCH">
        <Head title="RECEIVE PRODUCTS-BRANCH" />
        <JetValidationErrors class="mb-4" />
        <div
            v-if="hasData"
            class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4"
        >
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
                            v-model="user_receivables_params.search"
                            type="text"
                            id="table-search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-80 pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for products"
                        />
                    </div>
                    <div>
                        <select
                            v-model="user_receivables_params.product_type"
                            class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected value="">Choose type</option>
                            <option value="chicken">Chicken</option>
                            <option value="pork">Pork</option>
                            <option value="beef">Beef</option>
                        </select>
                    </div>
                </div>
            </div>
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400"
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
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('name')"
                                >Product name
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            user_receivables_params.field ===
                                                'name' &&
                                            user_receivables_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            user_receivables_params.field ===
                                                'name' &&
                                            user_receivables_params.direction ===
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
                                            user_receivables_params.field ===
                                                'type' &&
                                            user_receivables_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            user_receivables_params.field ===
                                                'type' &&
                                            user_receivables_params.direction ===
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
                                >receivables
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            user_receivables_params.field ===
                                                'quantity' &&
                                            user_receivables_params.direction ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            user_receivables_params.field ===
                                                'quantity' &&
                                            user_receivables_params.direction ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3">Unit</th>
                        <th scope="col" class="px-6 py-3">
                            Estimated time<br />
                            of arrival
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <row-receive-products
                        v-for="product in receivables.data"
                        :key="product.id"
                        :stock="product"
                        :isCheckedAll="isCheckedAll"
                        :checkboxChange="onCheckboxChange"
                    >
                    </row-receive-products>
                </tbody>
            </table>
        </div>
        <div v-else>
            <empty label="Receive Products"></empty>
        </div>
        <div v-if="hasData" class="pb-20 pt-8 flex justify-end items-center">
            <div class="flex gap-4">
                <jet-button @click="onReceivedSelected"
                    >Receive Selected</jet-button
                >
                <jet-button @click="onReceivedSelectedAll"
                    >Receive All</jet-button
                >
            </div>
        </div>
        <pagination :links="receivables.links" />
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
import RowReceiveProducts from "./RowReceiveProducts.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
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
        RowReceiveProducts,
        JetValidationErrors,
        DocumentDownloadIcon,
        Empty,
    },
    props: {
        receivables: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            isShow: false,
            productId: null,
            user_receivables_params: {
                search: null,
                field: null,
                direction: null,
                product_type: "",
            },
            isCheckedAll: false,
            selectedReceivables: [],
        };
    },
    computed: {
        hasData() {
            return this.receivables.data.length > 0;
        },
    },
    methods: {
        openConfirmation(id) {
            this.productId = id;
            this.isShow = true;
        },

        sort(field) {
            this.user_receivables_params.field = field;
            this.user_receivables_params.direction =
                this.user_receivables_params.direction === "asc"
                    ? "desc"
                    : "asc";
        },
        checkAll(event) {
            this.isCheckedAll = event.target.checked;

            if (this.isCheckedAll) {
                this.selectedReceivables = this.receivables.data.map(
                    (item) => item.id
                );
            } else {
                this.selectedReceivables = [];
            }

            console.log(this.selectedReceivables);
        },
        onReceivedSelected() {
            this.$inertia.visit(route("receive-products.received"), {
                method: "POST",
                data: {
                    ids: this.selectedReceivables,
                },
                preserveState: true,
                preserveScroll: true,
                onBefore: (visit) => {},
                onSuccess: (response) => {},
            });
        },
        onReceivedSelectedAll() {
            let receivables = this.receivables.data.map((item) => item.id);
            this.$inertia.visit(route("receive-products.received"), {
                method: "POST",
                data: {
                    ids: receivables,
                },
                preserveState: true,
                preserveScroll: true,
                onBefore: (visit) => {},
                onSuccess: (response) => {},
            });
        },
        onCheckboxChange(id, checked) {
            if (checked) {
                this.selectedReceivables.push(id);
            } else {
                this.selectedReceivables.splice(
                    this.selectedReceivables.indexOf(id)
                );
            }
        },
    },
    watch: {
        user_receivables_params: {
            handler: throttle(function () {
                let user_receivables_params = pickBy(
                    this.user_receivables_params
                );

                this.$inertia.get(
                    this.route("receive-products.index"),
                    user_receivables_params,
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
