<template>
    <table-layout title="ISSUE PRODUCTS/BRANCH">
        <Head title="ISSUE PRODUCTS-BRANCH" />
        <div class="my-4 flex">
            <div class="w-60">
                <Multiselect
                    v-model="selectedProduct"
                    :options="product_dropdown"
                    :searchable="true"
                    valueProp="id"
                    trackBy="name"
                    label="name"
                    placeholder="Select Product"
                />
            </div>
            <div>asdsdsad</div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">Product name</th>
                        <th scope="col" class="px-6 py-3">Quantity</th>
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
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
                        >
                            Microsoft Surface Pro
                        </th>
                        <td class="px-6 py-4">White</td>
                        <td class="px-6 py-4">Laptop PC</td>
                        <td class="px-6 py-4">$1999</td>
                        <td class="px-6 py-4">$1999</td>
                        <td class="px-6 py-4 text-right">
                            <a
                                href="#"
                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                                >Edit</a
                            >
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </table-layout>
</template>

<script>
import { Link, Head } from "@inertiajs/inertia-vue3";
import Multiselect from "@vueform/multiselect";
import TableLayout from "@/Layouts/TableLayout.vue";
export default {
    components: {
        TableLayout,
        Link,
        Head,
        Multiselect,
    },
    name: "IssueProductsIndex",
    props: {
        product_dropdown: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            products: [],
            selectedProduct: "",
        };
    },
    methods: {
        onSearchProduct(search) {},
        onSelectedProduct(product) {},
    },
};
</script>
<style src="@vueform/multiselect/themes/default.css"></style>
