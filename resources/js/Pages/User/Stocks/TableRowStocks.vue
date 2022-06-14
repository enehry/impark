<template>
    <tr
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
    >
        <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
        >
            {{ stock.name }}
        </th>
        <td class="px-6 py-4 uppercase">{{ stock.type }}</td>
        <td class="px-6 py-4">{{ stock.price }}</td>
        <td class="px-6 py-4">{{ stock.quantity }}</td>
        <td @dblclick="toggleEdit()" class="px-6 py-4">
            <div>
                <input
                    @keyup.enter="submit"
                    class="w-10 border-2 border-gray-400 rounded"
                    v-if="isEditing"
                    v-model="reordering_point"
                />
                <span v-else>{{ reordering_point }}</span>
            </div>
        </td>
        <td class="px-6 py-4">
            {{ stock.maximum_shelf_life }}
        </td>
        <td @dblclick="toggleEdit()" class="px-6 py-4">
            <div>
                <input
                    @keyup.enter="submit"
                    class="w-10 border-2 border-gray-400 rounded"
                    v-if="isEditing"
                    v-model="default_kg_per_day"
                />
                <span v-else>{{ default_kg_per_day }}</span>
            </div>
        </td>
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
    methods: {
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
