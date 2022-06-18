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
            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
        >
            {{ stock.name }}
        </th>
        <td class="px-6 py-4 uppercase">{{ stock.type }}</td>
        <td class="px-6 py-4">{{ stock.quantity }}</td>
        <td class="px-6 py-4">KG</td>
        <td class="px-6 py-4">{{ date }}</td>
        <td class="pr-4">
            <button
                @click.prevent="submit"
                v-if="isEditing"
                class="font-medium text-green-600 dark:text-green-500"
            >
                <SaveIcon class="w-4 h-4" />
            </button>
            <button
                @click.prevent="toggleEdit"
                v-else
                class="font-medium text-blue-600 dark:text-blue-500"
            >
                <PencilIcon class="w-4 h-4" />
            </button>
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
        isCheckedAll: {
            type: Boolean,
            default: false,
        },
        checkboxChange: {
            type: Function,
            required: true,
        },
    },
    data() {
        return {
            isEditing: false,
            reordering_point: this.stock.users_rop
                ? this.stock.users_rop
                : this.stock.reordering_point,
            default_kg_per_day: this.stock.users_kg_per_day
                ? this.stock.users_kg_per_day
                : this.stock.default_kg_per_day,
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
                "MMM DD, YY HH:mm"
            );
        },
    },
    methods: {
        onCheckboxChange($event) {
            this.checkboxChange(this.stock.id, $event.target.checked);
        },
        onChangeTextBox(event) {
            console.log(event.target.value);
        },
        toggleEdit() {
            this.isEditing = true;
        },
        cancel() {
            this.isEditing = false;
        },
        submit() {
            this.$inertia.post(
                route("stocks.forecast.update"),
                {
                    stock_id: this.stock.stock_id,
                    reordering_point: this.reordering_point,
                    default_kg_per_day: this.default_kg_per_day,
                },
                {
                    preserveState: true,
                }
            );
            this.isEditing = false;
        },
    },
};
</script>

<style></style>
