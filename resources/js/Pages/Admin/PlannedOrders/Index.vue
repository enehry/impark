<template>
    <table-layout
        title="Planned Orders"
        note="Planned orders are orders came from the branch forecasting, 
        clicking convert will move the product to the issue orders page."
    >
        <Head title="Planned Orders" />
        <tooltip
            id="tooltip-archived-orders"
            label="Archived planned orders"
        ></tooltip>
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
            class="flex justify-between sm:items-center items-start p-4 bg-white dark:bg-gray-800 rounded-t-lg shadow-md"
        >
            <div class="block sm:flex items-center gap-4">
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
                            v-model="planned_orders_params.search"
                            type="text"
                            id="table-search-planned-orders"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-80 pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for products"
                        />
                    </div>
                </div>
                <div class="mt-1">
                    <label for="table-branch" class="sr-only">Branch</label>
                    <select
                        v-model="planned_orders_params.branch"
                        class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option selected :value="null">Choose branch</option>
                        <option
                            v-for="branch in po_branches"
                            :value="branch.id"
                            :key="branch.id"
                        >
                            {{ branch.name }}
                        </option>
                    </select>
                </div>
            </div>
            <!-- <div class="flex justify-end gap-2 sm:mt-0 mt-2">
                <Link :href="route('planned-orders.all-trashed')">
                    <button
                        data-tooltip-target="tooltip-archived-orders"
                        class="bg-red-500 text-white p-2 rounded-full"
                    >
                        <trash-icon class="w-4 h-4" />
                    </button>
                </Link>
            </div> -->
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-b-lg w-full">
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
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
                        <th scope="col" class="px-6 py-3">Branch</th>
                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-2 items-center"
                                @click.prevent="sort('products.name')"
                                >Product name
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            planned_orders_params.field ===
                                                'products.name' &&
                                            planned_orders_params.order ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            planned_orders_params.field ===
                                                'products.name' &&
                                            planned_orders_params.order ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3">Unit</th>

                        <th scope="col" class="px-6 py-3 cursor-pointer">
                            <span
                                class="flex gap-2 items-center"
                                @click.prevent="
                                    sort('planned_orders.order_quantity')
                                "
                                >Requested <br />
                                Stocks(KG)
                                <div class="w-4 h-4">
                                    <sort-ascending-icon
                                        v-if="
                                            planned_orders_params.field ===
                                                'planned_orders.order_quantity' &&
                                            planned_orders_params.order ===
                                                'asc'
                                        "
                                    />
                                    <sort-descending-icon
                                        v-if="
                                            planned_orders_params.field ===
                                                'planned_orders.order_quantity' &&
                                            planned_orders_params.order ===
                                                'desc'
                                        "
                                    />
                                </div>
                            </span>
                        </th>
                        <th scope="col" class="px-6 py-3">Date</th>
                        <!-- <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Edit</span>
                            <span class="sr-only">Delete</span>
                        </th> -->
                    </tr>
                </thead>
                <tbody v-if="hasData">
                    <table-row-planned-orders
                        v-for="planned_order in planned_orders_admin"
                        :key="planned_order.id"
                        :planned_order="planned_order"
                        :checkboxChange="onCheckboxChange"
                        :isCheckedAll="isCheckedAll"
                    >
                    </table-row-planned-orders>
                </tbody>
                <tbody v-else>
                    <tr class="text-center">
                        <td colspan="7">
                            <Empty label="Planned orders"> </Empty>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="hasData" class="pb-20 pt-8 flex justify-end items center">
            <!-- <jet-button @click="showConfirmationDialog('cancel')">
                Cancel All
            </jet-button> -->
            <div class="flex gap-4">
                <jet-button @click="showConfirmationDialog('selected')">
                    Convert Selected
                </jet-button>
                <jet-button @click="showConfirmationDialog('all')">
                    Convert All
                </jet-button>
            </div>
        </div>
        <jet-dialog-modal :show="isShow">
            <template #title>
                <div class="flex gap-2 items-center">
                    <InformationCircleIcon class="h-5 w-5 text-amber-500" />
                    <h1 class="font-medium">Planned Orders Confirmation!</h1>
                </div>
            </template>
            <template #content>
                {{ message.title }}<br />
                {{ message.content }}
            </template>
            <template #footer>
                <JetSecondaryButton
                    :class="{ 'opacity-25': isLoading }"
                    :disabled="isLoading"
                    @click="isShow = false"
                >
                    Cancel
                </JetSecondaryButton>
                <jet-button
                    class="ml-3"
                    :class="{ 'opacity-25': isLoading }"
                    :disabled="isLoading"
                    @click.prevent="submit"
                    >OK</jet-button
                >
            </template>
        </jet-dialog-modal>
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import {
    InformationCircleIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    TrashIcon,
} from "@heroicons/vue/outline";
import Tooltip from "@/Components/Tooltip.vue";
import TableRowPlannedOrders from "./TableRowPlannedOrders.vue";
import JetButton from "@/Jetstream/Button.vue";
import Empty from "@/Components/Empty.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
export default {
    components: {
        TableLayout,
        Head,
        TrashIcon,
        Link,
        Tooltip,
        TableRowPlannedOrders,
        JetButton,
        Empty,
        SortAscendingIcon,
        SortDescendingIcon,
        JetDialogModal,
        InformationCircleIcon,
        JetSecondaryButton,
        JetValidationErrors,
    },
    name: "PlannedOrdersIndex",
    props: {
        planned_orders_admin: {
            type: Array,
            default: () => [],
        },
        po_branches: {
            type: Array,
            default: () => [],
        },
        po_filters: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            selected_planned_orders: [],
            isCheckedAll: false,
            planned_orders_params: {
                search: this.po_filters.search,
                field: this.po_filters.field,
                order: this.po_filters.order,
                type: this.po_filters.type,
                branch: this.po_filters.branch,
            },
            isShow: false,
            message: {
                title: "",
                content: "",
            },
            error: {
                message: "",
                title: "",
            },
            type: null,
            isLoading: false,
        };
    },
    computed: {
        hasData() {
            return this.planned_orders_admin.length > 0;
        },
    },
    methods: {
        onCheckboxChange(id, checked) {
            if (checked) {
                this.selected_planned_orders.push(id);
            } else {
                this.selected_planned_orders.splice(
                    this.selected_planned_orders.indexOf(id),
                    1
                );
            }
        },
        checkAll($event) {
            if ($event.target.checked) {
                this.selected_planned_orders = this.planned_orders_admin.map(
                    (planned_order) => planned_order.id
                );
                this.isCheckedAll = true;
            } else {
                this.selected_planned_orders = [];
                this.isCheckedAll = false;
            }
            console.log(this.selected_planned_orders);
        },
        showConfirmationDialog(type) {
            this.type = type;
            if (type === "cancel") {
                this.message.title =
                    "Are you sure you want to cancel all planned orders?";
                this.message.content =
                    "Cancelled planned orders will be moved to trash and branch cannot forecast the trashed product," +
                    " you need to delete it permanently in order the branch can forecast it again.";
                this.isShow = true;
            } else if (type === "all") {
                this.message.title = `Are you sure you want to convert all planned orders?`;
                this.message.content =
                    "All data in the planned orders will be converted and save in receive products of specific branch.";
                this.isShow = true;
                return;
            }
            // check if there is selected planned orders
            else if (type === "selected") {
                if (this.selected_planned_orders.length <= 0) {
                    this.error.title = "No selected planned orders";
                    this.error.message =
                        "Please select at least one planned order to convert";
                    return;
                }
                this.message.title = `Are you sure you want to convert ${this.selected_planned_orders.length} planned orders?`;
                this.message.content =
                    "All data in the planned orders will be converted and save in receive products of specific branch.";
                this.isShow = true;
            }
        },
        submit() {
            this.isLoading = true;
            if (this.type === "all") {
                this.onConvertAll();
                return;
            } else if (this.type === "selected") {
                this.onConvert();
                return;
            } else if (this.type === "cancel") {
                this.onCancelAll();
                return;
            }
        },
        onConvertAll() {
            this.$inertia.visit(route("planned-orders.convert"), {
                method: "POST",
                data: {
                    ids: this.planned_orders_admin.map(
                        (planned_order) => planned_order.id
                    ),
                },
                onBefore: (visit) => {},
                onSuccess: (response) => {
                    this.isShow = false;
                    this.isLoading = false;
                },
            });
        },
        onConvert() {
            this.$inertia.visit(route("planned-orders.convert"), {
                method: "POST",
                data: {
                    ids: this.selected_planned_orders,
                },
                preserveState: true,
                preserveScroll: true,
                onBefore: (visit) => {},
                onSuccess: (response) => {
                    this.isShow = false;
                    this.isLoading = false;
                },
            });
        },
        onCancelAll() {
            this.$inertia.visit(route("planned-orders.cancel-all"), {
                method: "POST",
                data: {
                    ids: this.planned_orders_admin.map(
                        (planned_order) => planned_order.id
                    ),
                },
                onBefore: (visit) => {},
                onSuccess: (response) => {
                    this.isShow = false;
                    this.isLoading = false;
                },
            });
        },
        sort(field) {
            this.planned_orders_params.field = field;
            this.planned_orders_params.order =
                this.planned_orders_params.order === "asc" ? "desc" : "asc";
        },
    },
    watch: {
        planned_orders_params: {
            handler: throttle(function () {
                let params = pickBy(this.planned_orders_params);

                this.$inertia.get(this.route("planned-orders.index"), params, {
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
