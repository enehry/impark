<template>
    <tr
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
    >
        <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap"
        >
            {{ product.name }}
        </th>
        <td class="px-6 py-4 uppercase">{{ product.type }}</td>
        <td @dblclick="toggleEdit()" class="px-6 py-4">
            <div>
                <input
                    @keyup.enter="submit"
                    class="w-10 border-2 border-gray-400 rounded"
                    v-if="isEditing"
                    v-model="price"
                />
                <span v-else>{{ product.price }}</span>
            </div>
        </td>
        <td class="px-6 py-4">0</td>
        <td class="px-6 py-4">
            {{ product.reordering_point }}
        </td>
        <td class="px-6 py-4">
            {{ product.maximum_shelf_life }}
        </td>
        <td class="flex items-center gap-4 py-4 pr-4">
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
    name: "TableRow",
    props: {
        product: {
            type: Object,
            required: true,
        },
    },
    components: {
        PencilIcon,
        SaveIcon,
    },
    data() {
        return {
            isEditing: false,
            price: this.product.price,
        };
    },
    methods: {
        toggleEdit() {
            this.isEditing = !this.isEditing;
            console.log(this.isEditing);
        },
        submit() {
            // update product price inertia
            console.log(this.price);
            this.$inertia.put(route("bom.update-price"), {
                id: this.product.id,
                price: this.price,
            });

            this.isEditing = false;
        },
    },
};
</script>

<style></style>
