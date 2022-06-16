<template>
    <tr
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
    >
        <th
            scope="row"
            class="uppercase px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
        >
            {{ forecast_stock.name }}
        </th>
        <td class="px-6 py-4 uppercase">{{ forecast_stock.type }}</td>
        <td class="px-6 py-4">{{ forecast_stock.quantity }}</td>
        <td class="px-6 py-4">KG</td>
        <td class="px-6 py-4">{{ forecast_stock.rop }}</td>
        <td class="px-6 py-4">{{ forecast_stock.kg_per_day }}</td>
        <td class="px-6 py-4">{{ forecast_stock.forecast_quantity }}</td>
    </tr>
</template>

<script>
import { PencilIcon, SaveIcon } from "@heroicons/vue/outline";
export default {
    name: "TableRowForecasting",
    props: {
        forecast_stock: {
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
    methods: {},
};
</script>

<style></style>
