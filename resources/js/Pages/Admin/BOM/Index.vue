<template>
    <table-layout
        title="Bill of Materials"
        note="You can edit
                the product by clicking the pencil icon, sort by clicking the
                column header."
    >
        <Head title="Bill of Materials" />
        <div
            class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 md:rounded-t-lg shadow-md"
        >
            <div class="flex gap-2">
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
                            class="bg-gray-50 sm:w-80 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for products"
                        />
                    </div>
                </div>
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
                <tbody v-if="hasData">
                    <table-row-bom
                        v-for="product in products.data"
                        :key="product.id"
                        :product="product"
                    >
                    </table-row-bom>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="6" class="px-6 py-4">
                            <Empty label="Bill of materials" />
                        </td>
                    </tr>
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
import JetButton from "@/Jetstream/Button.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import Pagination from "@/Components/Pagination.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import JetModal from "@/Jetstream/Modal.vue";
import TableRowBom from "./TableRowBOM.vue";
import Empty from "@/Components/Empty.vue";
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
        TableRowBom,
        Empty,
    },
    props: {
        products: {
            type: Object,
            default: () => {},
        },
        bom_filter: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            params: {
                search: this.bom_filter.search,
                field: this.bom_filter.field,
                direction: this.bom_filter.direction,
                type: this.bom_filter.type,
            },
            show: false,
            product: null,
        };
    },
    computed: {
        hasData() {
            return this.products.data.length > 0;
        },
    },
    methods: {
        destroy() {
            this.isShow = false;
            if (this.productId) {
                this.$inertia.visit(
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
                let params = pickBy(this.params);

                this.$inertia.get(this.route("bom.index"), params, {
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
