<template>
    <tr
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
    >
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
        <td class="px-6 py-4">
            {{ planned_order.quantity }}
        </td>
        <td class="px-6 py-4">
            {{ date }}
        </td>
        <td class="px-6 py-4 flex gap-4 justify-end items-center">
            <button
                @click="onRestore"
                class="font-medium text-green-500 dark:text-green-500 hover:underline"
            >
                <refresh-icon class="w-4 h-5"></refresh-icon>
            </button>
            <button
                @click="onDelete"
                class="font-medium text-red-500 dark:text-red-500 hover:underline"
            >
                <trash-icon class="w-4 h-5"></trash-icon>
            </button>
        </td>
    </tr>
</template>

<script>
import {
    PencilIcon,
    SaveIcon,
    RefreshIcon,
    TrashIcon,
} from "@heroicons/vue/outline";
import moment from "moment";
export default {
    components: {
        PencilIcon,
        SaveIcon,
        RefreshIcon,
        TrashIcon,
    },
    props: {
        planned_order: {
            type: Object,
            required: true,
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
        onEdit() {
            this.isEditMode = true;
        },
        onRestore() {
            this.$inertia.visit(
                route("planned-orders.restore", {
                    id: this.planned_order.id,
                }),
                {
                    method: "POST",
                    preserveScroll: true,
                    preserveState: true,
                }
            );
        },
        onDelete() {
            this.$inertia.visit(
                route("planned-orders.delete", {
                    id: this.planned_order.id,
                }),
                {
                    method: "DELETE",
                    preserveScroll: true,
                    preserveState: true,
                }
            );
        },
        // onSave() {
        //     this.$inertia.visit(
        //         route("planned-orders.update", {
        //             planned_order: this.planned_order.id,
        //         }),
        //         {
        //             method: "PUT",
        //             data: {
        //                 quantity: this.quantity,
        //             },
        //             onSuccess: () => {
        //                 this.isEditMode = false;
        //             },
        //             preserveScroll: true,
        //             preserveState: true,
        //         }
        //     );
        // },
    },
};

// <div class="flex items-center">
//                 <button
//                     v-if="isEditMode"
//                     @click.prevent="onSave"
//                     class="font-medium text-green-600 dark:text-green-500 hover:underline"
//                 >
//                     <save-icon class="w-4 h-5"> </save-icon>
//                 </button>
//                 <button
//                     v-else
//                     @click.prevent="onEdit"
//                     class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
//                 >
//                     <pencil-icon class="w-4 h-5"> </pencil-icon>
//                 </button>
//             </div>
</script>

<style></style>
