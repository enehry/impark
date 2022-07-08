<template>
    <tr
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
    >
        <td class="w-4 p-4">
            <div class="flex items-center">
                <input
                    :checked="isCheckedAll"
                    @change="onCheckboxChange($event)"
                    id="checkbox-table-1"
                    type="checkbox"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                />
                <label for="checkbox-table-1" class="sr-only">checkbox</label>
            </div>
        </td>
        <th
            scope="row"
            class="uppercase px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
        >
            {{ planned_order.branch_name }}
        </th>
        <td class="px-6 py-4 uppercase">
            {{ planned_order.product_name }}
        </td>
        <td class="px-6 py-4">KG</td>
        <td
            @dblclick="onEdit"
            @keypress.enter="onSave"
            class="px-6 py-4 cursor-pointer"
        >
            <div v-if="isEditMode">
                <input
                    class="text-sm h-5 w-14 pl-0.5 p-0 border border-gray-400 rounded-md"
                    type="number"
                    v-model="quantity"
                />
            </div>
            <span v-else> {{ planned_order.quantity }}</span>
        </td>
        <td class="px-6 py-4">
            {{ date }}
        </td>
        <td class="px-6 py-4 flex gap-4 justify-end items-center">
            <div class="flex items-center">
                <button
                    v-if="isEditMode"
                    @click.prevent="onSave"
                    class="font-medium text-green-600 dark:text-green-500 hover:underline"
                >
                    <save-icon class="w-4 h-5"> </save-icon>
                </button>
                <button
                    v-else
                    @click.prevent="onEdit"
                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                >
                    <pencil-icon class="w-4 h-5"> </pencil-icon>
                </button>
            </div>

            <button
                @click="onTrash"
                class="font-medium text-red-600 dark:text-red-500 hover:underline"
            >
                <trash-icon class="w-4 h-5"></trash-icon>
            </button>
        </td>
    </tr>
</template>

<script>
import { TrashIcon, PencilIcon, SaveIcon } from "@heroicons/vue/outline";
import moment from "moment";
export default {
    components: {
        TrashIcon,
        PencilIcon,
        SaveIcon,
    },
    props: {
        planned_order: {
            type: Object,
            required: true,
        },
        checkboxChange: {
            type: Function,
            required: true,
        },
        isCheckedAll: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            isEditMode: false,
            quantity: this.planned_order.quantity,
        };
    },
    computed: {
        date() {
            // return date in MM/DD/YYYY with time in HH:MM
            return moment(this.planned_order.created_at).format(
                "MMM DD, YY HH:mm"
            );
        },
    },
    methods: {
        onCheckboxChange($event) {
            this.checkboxChange(this.planned_order.id, $event.target.checked);
        },
        onEdit() {
            this.isEditMode = true;
        },
        onTrash() {
            this.$inertia.visit(
                route("planned-orders.trash", {
                    id: this.planned_order.id,
                }),
                {
                    method: "DELETE",
                    preserveScroll: true,
                    preserveState: true,
                }
            );
        },
        onSave() {
            this.$inertia.visit(
                route("planned-orders.update", {
                    planned_order: this.planned_order.id,
                }),
                {
                    method: "PATCH",
                    data: {
                        quantity: this.quantity,
                    },
                    onSuccess: () => {
                        this.isEditMode = false;
                    },
                    preserveScroll: true,
                    preserveState: true,
                }
            );
        },
    },
};
</script>

<style></style>
