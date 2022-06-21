<template>
    <tr
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
    >
        <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
        >
            {{ sale.name }}
        </th>
        <td class="px-6 py-4">{{ sale.type }}</td>
        <td class="px-6 py-4">{{ sale.sold_quantity }}</td>
        <td class="px-6 py-4">{{ sale.price }}</td>
        <td class="px-6 py-4">
            {{ sale.total_sales }}
        </td>
        <td class="px-6 py-4">
            {{ date }}
        </td>
    </tr>
</template>

<script>
import moment from "moment";
export default {
    props: {
        sale: {
            type: Object,
            required: true,
        },
    },
    computed: {
        // format date mm/dd/yyyy
        date() {
            return moment(this.sale.date).format("MM/DD/YYYY");
        },
    },
};
</script>

<style></style>
