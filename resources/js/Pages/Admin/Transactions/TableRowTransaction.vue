<template>
    <tr
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
    >
        <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
        >
            {{ stock.branch_name }}
        </th>
        <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
        >
            {{ stock.name }}
        </th>
        <td class="px-6 py-4 uppercase">{{ stock.type }}</td>
        <td class="px-6 py-4">{{ stock.quantity }}</td>
        <td class="px-4 py-4">
            <span
                class="font-medium px-2 rounded-full"
                :class="{
                    'bg-amber-300 text-amber-700': is_not_delivered,
                    'text-green-700 bg-green-300': !is_not_delivered,
                }"
                >{{ formatted_delivered_at }}</span
            >
        </td>
        <td class="px-4 py-4">
            <span
                class="font-medium px-2 rounded-full"
                :class="{
                    'bg-amber-300 text-amber-700': is_not_received,
                    'text-green-700 bg-green-300': !is_not_received,
                }"
                >{{ formatted_received_at }}</span
            >
        </td>
    </tr>
</template>

<script>
import { PencilIcon, SaveIcon } from "@heroicons/vue/outline";
import moment from "moment";
export default {
    name: "TableRowStocks",
    props: {
        stock: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            isEditing: false,
        };
    },
    components: {
        PencilIcon,
        SaveIcon,
    },
    computed: {
        date() {
            // return date in MM/DD/YYYY with time in HH:MM
            return moment(this.stock.expected_delivery_date).format(
                "MM/DD/YY HH:mm"
            );
        },
        formatted_delivered_at() {
            return this.stock.formatted_delivered_at === null
                ? "Not Delivered"
                : this.stock.formatted_delivered_at;
        },
        formatted_received_at() {
            return this.stock.formatted_received_at === null
                ? "Not Received"
                : this.stock.formatted_received_at;
        },
        is_not_delivered() {
            return this.stock.formatted_delivered_at === null;
        },
        is_not_received() {
            return this.stock.formatted_received_at === null;
        },
    },
    methods: {},
};
</script>

<style></style>
