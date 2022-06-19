<template>
    <table-layout
        title="Products"
        note="You can edit the product by clicking the pencil icon and delete with trash icon, clicking the column header will sort the product."
    >
        <Head title="Products" />
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
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-80 pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for products"
                        />
                    </div>
                </div>
                <div class="flex gap-2">
                    <div class="mt-1">
                        <label for="table-branch" class="sr-only">Branch</label>
                        <select
                            v-model="params.branch"
                            class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected :value="null">
                                Choose branch
                            </option>
                            <option
                                v-for="branch in p_branches"
                                :value="branch.id"
                                :key="branch.id"
                            >
                                {{ branch.name }}
                            </option>
                        </select>
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

            <div class="flex gap-2 items-center">
                <a
                    :href="route('products.pdf', params)"
                    class="text-red-500 hover:text-red-300 uppercase text-xs flex items-center"
                >
                    <DocumentDownloadIcon class="w-6 h-6" />
                    <div class="text-gray-500">PDF</div>
                </a>
                <a
                    :href="route('products.excel', params)"
                    class="text-green-500 hover:text-green-300 uppercase text-xs flex items-center"
                >
                    <DocumentDownloadIcon class="w-6 h-6" />
                    <div class="text-gray-500">Excel</div>
                </a>

                <Link
                    data-tooltip-target="tooltip-create-product"
                    :href="route('products.create')"
                >
                    <button
                        class="bg-green-500 text-white p-2 rounded-full hover:bg-green-700"
                    >
                        <PlusIcon class="w-4 h-4" />
                    </button>
                </Link>

                <tooltip
                    id="tooltip-create-product"
                    label="Create New Product"
                ></tooltip>
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
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-1"
                                @click.prevent="sort('quantity')"
                                >STOCKS
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            params.field === 'quantity' &&
                                            params.direction === 'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            params.field === 'quantity' &&
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
                    <tr
                        v-for="stock in products_admin.data"
                        :key="stock.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
                        >
                            {{ stock.name }}
                        </th>
                        <td class="px-6 py-4 uppercase">
                            {{ stock.type }}
                        </td>
                        <td class="px-6 py-4">{{ stock.price }}</td>
                        <td class="px-6 py-4">{{ stock.quantity }}</td>
                        <td class="px-6 py-4">
                            {{ stock.reordering_point }}
                        </td>
                        <td class="px-6 py-4">
                            {{ stock.maximum_shelf_life }}
                        </td>
                        <td class="flex items-center gap-4 py-4 pr-4">
                            <Link
                                :href="
                                    route('products.edit', {
                                        id: stock.id,
                                    })
                                "
                                class="font-medium text-blue-600 dark:text-blue-500"
                                ><PencilIcon class="w-4 h-4"
                            /></Link>

                            <button
                                @click.prevent="openConfirmation(stock.id)"
                                class="font-medium text-red-600 dark:text-red-500 hover:underline"
                            >
                                <TrashIcon class="w-4 h-4"></TrashIcon>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="7" class="px-6 py-4">
                            <Empty label="Products" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination :links="products_admin.links" />
        <jet-confirmation-modal :show="isShow">
            <template #title>
                <h1 class="font-medium">
                    Are you sure you want to delete this product ?
                </h1>
            </template>
            <template #content>
                You are about to delete this product. This action cannot be
                undone.
            </template>
            <template #footer>
                <button
                    @click="isShow = false"
                    class="border-2 border-red-500 hover:bg-red-500 hover:text-white text-red-500 font-medium rounded-md px-2 mr-2"
                >
                    Cancel
                </button>
                <jet-button @click.prevent="destroy()">OK</jet-button>
            </template>
        </jet-confirmation-modal>
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Link, Head } from "@inertiajs/inertia-vue3";
import Tooltip from "@/Components/Tooltip.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import pickBy from "lodash/pickBy";
import Pagination from "@/Components/Pagination.vue";
import throttle from "lodash/throttle";
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
        DocumentDownloadIcon,
        Empty,
    },
    props: {
        products_admin: {
            type: Object,
            default: () => {},
        },
        p_branches: {
            type: Array,
            default: () => [],
        },
        products_filter: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            isShow: false,
            productId: null,
            params: {
                search: this.products_filter.search,
                field: this.products_filter.field,
                direction: this.products_filter.direction,
                branch: this.products_filter.branch,
                type: this.products_filter.type,
            },
        };
    },
    computed: {
        hasData() {
            return this.products_admin.data.length > 0;
        },
    },
    methods: {
        openConfirmation(id) {
            this.productId = id;
            this.isShow = true;
        },
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
    },
    watch: {
        params: {
            handler: throttle(function () {
                let params = pickBy(this.params);

                this.$inertia.get(this.route("products.index"), params, {
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
