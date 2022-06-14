<template>
    <table-layout title="Bill of Materials">
        <Head title="Bill of Materials" />
        <tooltip id="bom-tip" label="Edit price" />
        <jet-modal :show="show" maxWidth="sm">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex justify-between items-center p-5 rounded-t border-b dark:border-gray-600"
                >
                    <h3
                        class="text-xl font-medium text-gray-700 dark:text-white uppercase"
                    >
                        UPDATE Product Price
                    </h3>
                    <button
                        type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        @click="show = false"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <p
                        class="text-base text-gray-700 dark:text-gray-300 uppercase"
                    >
                        Product name: {{ product.name }}
                    </p>
                    <p
                        class="text-base text-gray-700 dark:text-gray-300 uppercase"
                    >
                        Product Type: {{ product.type }}
                    </p>
                </div>
                <!-- Modal footer -->
                <div
                    class="flex justify-end items-center p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600"
                >
                    <button
                        @click="show = false"
                        type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-2 py-2 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                    >
                        Close
                    </button>
                    <JetButton type="submit">SAVE</JetButton>
                </div>
            </div>
        </jet-modal>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
            <div
                class="flex justify-between items-center p-4 bg-white dark:bg-gray-800"
            >
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
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-80 pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search for products"
                    />
                </div>
            </div>
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
                                            params.field === 'name' &&
                                            params.direction === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            params.field === 'name' &&
                                            params.direction === 'desc'
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
                                            params.field === 'type' &&
                                            params.direction === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            params.field === 'type' &&
                                            params.direction === 'desc'
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
                                            params.field === 'price' &&
                                            params.direction === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            params.field === 'price' &&
                                            params.direction === 'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3">Stocks</th>
                        <th scope="col" class="px-6 py-3">ROP</th>
                        <th scope="col" class="px-6 py-3">
                            Maximum <br />
                            Shelf Life
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <table-row
                        v-for="product in products.data"
                        :key="product.id"
                        :product="product"
                    >
                    </table-row>
                </tbody>
            </table>
        </div>
        <pagination :links="products.links" />
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Link, Head } from "@inertiajs/inertia-vue3";
import Tooltip from "@/Components/Tooltip.vue";
import JetButton from "@/JetStream/Button.vue";
import JetConfirmationModal from "@/JetStream/ConfirmationModal.vue";
import Pagination from "@/Components/Pagination.vue";
import throttle from "lodash/throttle";
import JetModal from "@/JetStream/Modal.vue";
import TableRow from "@/Components/TableRow.vue";
import {
    PlusIcon,
    DocumentTextIcon,
    PencilIcon,
    TrashIcon,
    SortAscendingIcon,
    SortDescendingIcon,
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
        JetModal,
        TableRow,
    },
    props: {
        products: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            params: {
                search: null,
                field: null,
                direction: null,
            },
            show: false,
            product: null,
        };
    },
    methods: {
        destroy() {
            this.isShow = false;
            if (this.productId) {
                this.$inertia.post(
                    route("products.destroy", { product: this.productId }),
                    { _method: "DELETE" }
                );
            }
        },
        sort(field) {
            this.params.field = field;
            this.params.direction =
                this.params.direction === "asc" ? "desc" : "asc";
        },
        showModal(product) {
            this.show = true;
            this.product = product;
        },
    },
    watch: {
        params: {
            handler: throttle(function () {
                this.$inertia.get(this.route("bom.index"), this.params, {
                    preserveState: true,
                    replace: true,
                });
            }, 300),
            deep: true,
        },
    },
};
</script>

<style></style>
