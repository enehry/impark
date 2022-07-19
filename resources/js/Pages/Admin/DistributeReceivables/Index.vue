<template>
    <table-layout title="Distribute Receivables">
        <Head title="Distribute Receivables" />
        <JetValidationErrors class="mb-4" />
        <div
            v-show="error.message"
            class="p-4 mb-4 text-sm text-red-700 bg-red-200 rounded-lg dark:bg-red-200 dark:text-red-800"
            role="alert"
        >
            <span class="font-medium">{{ error.title }}</span>
            {{ error.message }}
        </div>
        <div
            class="flex items-end md:items-center gap-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-md shadow-md mb-8"
        >
            <div class="flex md:flex-row flex-col items-center gap-4 w-2/3">
                <div class="w-full">
                    <label
                        for="countries"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"
                        >Select a branch</label
                    >
                    <select
                        v-model="selectedBranch"
                        id="branch"
                        class="bg-gray-50 w-full border text-gray-700 border-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option class="text-gray-400" value="null" selected>
                            Select branch
                        </option>
                        <option
                            v-for="branch in admin_branch_dropdown"
                            :key="branch.id"
                            :value="branch.id"
                        >
                            {{ branch.name }}
                        </option>
                        <option value="warehouse">Warehouse</option>
                    </select>
                </div>
                <div class="w-full">
                    <label
                        for="Type"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"
                        >Type (optional)</label
                    >
                    <select
                        v-model="productType"
                        id="type"
                        class="bg-gray-50 w-full border text-gray-700 border-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option class="text-gray-400" value="" selected>
                            Select type
                        </option>
                        <option value="pork">Pork</option>
                        <option value="chicken">Chicken</option>
                        <option value="beef">Beef</option>
                    </select>
                </div>
            </div>

            <div
                class="flex md:flex-row flex-col items-center justify-between gap-4 w-full"
            >
                <div class="w-full">
                    <label
                        for="products"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"
                        >Select product</label
                    >
                    <Multiselect
                        :options="filteredProducts"
                        :searchable="true"
                        valueProp="id"
                        trackBy="name"
                        label="name"
                        v-model="selected"
                        @select="onSelectProduct"
                        placeholder="Select Product"
                    />
                </div>
                <div class="sm:w-40 w-full">
                    <!-- input quantity -->
                    <label
                        for="countries"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"
                        >Quantity</label
                    >
                    <input
                        @keypress.enter="onAddProduct"
                        type="number"
                        v-model="quantity"
                        class="bg-gray-50 border w-full text-gray-700 border-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Quantity"
                    />
                </div>
            </div>
            <div class="w-20 flex justify-end">
                <div>
                    <jet-button @click.prevent="onAddProduct" class="mt-7">
                        <check-icon
                            class="h-6 w-6 text-white font-bold"
                        ></check-icon>
                    </jet-button>
                </div>
            </div>
        </div>
        <div v-if="addedDistributeReceivables.length" class="pb-20">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table
                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
                >
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                    >
                        <tr>
                            <th scope="col" class="px-6 py-3">Branch name</th>
                            <th scope="col" class="px-6 py-3">Product name</th>
                            <th scope="col" class="px-6 py-3">Quantity(KG)</th>
                            <th scope="col" class="px-6 py-3">Unit</th>
                            <th scope="col" class="px-6 py-3">Price</th>
                            <th scope="col" class="px-6 py-3">Total</th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="addedProduct in addedDistributeReceivables"
                            :key="addedProduct.id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                        >
                            <th
                                scope="row"
                                class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
                            >
                                {{ addedProduct.branch_name }}
                            </th>
                            <td
                                scope="row"
                                class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
                            >
                                {{ addedProduct.name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ addedProduct.quantity }}
                            </td>
                            <td class="px-6 py-4">KG</td>
                            <td class="px-6 py-4">{{ addedProduct.price }}</td>
                            <td class="px-6 py-4">
                                {{ addedProduct.quantity * addedProduct.price }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="removeAddedProduct(addedProduct.id)"
                                >
                                    <trash-icon
                                        class="text-red-500 w-4 h-4 hover:text-red-700"
                                    />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pt-8 flex justify-end">
                <JetButton type="button" @click="submit"> Proceed </JetButton>
            </div>
        </div>
        <div class="mt-8" v-else>
            <Empty label="Distribute Receivables"></Empty>
        </div>
        <jet-modal :show="isShowModal" maxWidth="sm">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex justify-between items-center p-5 rounded-t border-b dark:border-gray-600"
                >
                    <h3
                        class="text-lg font-medium text-gray-700 dark:text-white"
                    >
                        Issue product receipt
                    </h3>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6 w-full bg-yellow-100">
                    <div class="text-sm text-gray-600">
                        <p>
                            <span class="text-red-600">Note: </span>Check issue
                            product receipt carefully!
                        </p>
                        <p class="font-medium">Date : {{ today }}</p>
                    </div>
                    <table>
                        <tr v-for="addedProduct in addedDistributeReceivables">
                            <td
                                class="px-6 py-1 font-bold text-gray-700 uppercase"
                            >
                                {{ addedProduct.quantity }} KG
                            </td>
                            <td class="px-6 py-1 text-gray-600 uppercase">
                                {{ addedProduct.name }}
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- Modal footer -->
                <div
                    class="flex justify-end items-center p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600"
                >
                    <a :href="route('issue-products.pdf')">
                        <JetButton @click="closeModal" type="button">
                            Confirm
                        </JetButton>
                    </a>
                </div>
            </div>
        </jet-modal>
    </table-layout>
</template>

<script>
import { Link, Head, Inertia } from "@inertiajs/inertia-vue3";
import Multiselect from "@vueform/multiselect";
import TableLayout from "@/Layouts/TableLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import Empty from "@/Components/Empty.vue";
import JetModal from "@/Jetstream/Modal.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";

import { TrashIcon } from "@heroicons/vue/outline";
import { CheckIcon } from "@heroicons/vue/solid";
export default {
    components: {
        TableLayout,
        Link,
        Head,
        Multiselect,
        JetButton,
        Empty,
        TrashIcon,
        JetModal,
        CheckIcon,
        JetValidationErrors,
    },
    name: "IssueProductsIndex",
    props: {
        admin_products_dropdown: {
            type: Array,
            default: () => [],
        },
        admin_branch_dropdown: {
            type: Array,
            default: () => [],
        },
        receivables_filter: {
            type: Object,
            default: () => {},
        },
    },
    computed: {
        filteredProducts() {
            if (this.productType) {
                return this.admin_products_dropdown.filter((product) => {
                    return product.type === this.productType;
                });
            }

            return this.admin_products_dropdown;
        },
        // get today date
        today() {
            var today = new Date();
            return today.toLocaleDateString();
        },
    },
    data() {
        return {
            addedDistributeReceivables: [],
            selectedProduct: {},
            // find branch by id
            selectedBranch: this.receivables_filter.branch_id,
            productType: "",
            quantity: 0,
            selected: "",
            error: {
                title: null,
                message: null,
            },
            isShowModal: false,
        };
    },
    methods: {
        onProceed() {
            this.isShowModal = true;
        },
        onSelectProduct(id) {
            this.selectedProduct = this.admin_products_dropdown.find(
                (product) => product.id == id
            );
        },
        closeModal() {
            this.isShowModal = false;
            this.addedDistributeReceivables = [];
        },
        onAddProduct() {
            // check if product is already added
            // if (
            //     this.addedDistributeReceivables.find(
            //         (product) => product.id == this.selectedProduct.id
            //     )
            // ) {
            //     this.error.title = "Product already added!";
            //     this.error.message = "You have already added this product";
            //     return;
            // }
            if (!(this.selectedProduct.id && this.quantity)) {
                this.error.title = "Error empty fields!";
                this.error.message =
                    "Please select a product and enter quantity";
                return;
            }

            if (this.selectedBranch == "") {
                this.error.title = "Error empty branch!";
                this.error.message = "Please select a branch";
                return;
            }

            const branch_name =
                this.selectedBranch === "warehouse"
                    ? "warehouse"
                    : this.admin_branch_dropdown.find(
                          (branch) => branch.id == this.selectedBranch
                      ).name;

            this.addedDistributeReceivables.push({
                id: this.selectedProduct.id,
                name: this.selectedProduct.name,
                quantity: this.quantity,
                product_id: this.selectedProduct.product_id,
                price: this.selectedProduct.price,
                total: this.quantity * this.selectedProduct.price,
                branch_name: branch_name,
                branch_id: this.selectedBranch,
            });
            this.quantity = 0;
            this.selected = "";
            this.selectedProduct = {};
            this.error.title = null;
            this.error.message = null;
            return;
        },
        removeAddedProduct(id) {
            this.addedDistributeReceivables =
                this.addedDistributeReceivables.filter(
                    (product) => product.id != id
                );
        },
        submit() {
            if (!this.addedDistributeReceivables.length) {
                this.error.title = "No product added!";
                this.error.message = "Please add at least one product";
                return;
            }
            this.$inertia.visit(route("distribute-receivables.proceed"), {
                method: "post",
                data: {
                    distribute_receivables: this.addedDistributeReceivables,
                },
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    this.addedDistributeReceivables = [];
                },
            });
        },
    },
    watch: {
        selectedBranch: {
            handler: throttle(function () {
                this.$inertia.get(
                    this.route("distribute-receivables.index"),
                    { branch_id: this.selectedBranch },
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
<style src="@vueform/multiselect/themes/default.css"></style>
