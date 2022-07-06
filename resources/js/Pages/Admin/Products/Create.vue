<template>
    <table-layout title="Create Product" backRoute="products.index">
        <Head title="Create Product" />
        <form
            @submit.prevent="submit"
            class="w-full bg-white shadow-lg rounded-lg p-8 dark:bg-gray-800"
        >
            <div class="">
                <JetValidationErrors class="mb-4" />
                <div
                    class="flex flex-col md:flex-row w-full md:gap-8 gap-2 mt-4"
                >
                    <div class="w-full">
                        <JetLabel for="product-name" value="Product Name" />
                        <JetInput
                            v-model="form.name"
                            :hasError="form.errors.name !== undefined"
                            type="text"
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            id="user-name"
                            required
                            autofocus
                            placeholder="Pork Belly"
                        ></JetInput>
                        <JetInputError
                            v-if="form.errors.name"
                            :message="form.errors.name"
                        />
                    </div>
                    <div class="w-full">
                        <JetLabel for="Price" value="Price" />
                        <JetInput
                            v-model="form.price"
                            :hasError="form.errors.price !== undefined"
                            type="number"
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            placeholder="Price"
                            required
                        ></JetInput>
                        <JetInputError
                            v-if="form.errors.price"
                            :message="form.errors.price"
                        />
                    </div>
                </div>
                <div
                    class="flex flex-col md:flex-row w-full md:gap-8 gap-2 mt-4"
                >
                    <div class="w-full">
                        <JetLabel
                            for="re-ordering-point"
                            value="Reordering point(KG)"
                        />
                        <JetInput
                            v-model="form.reordering_point"
                            :hasError="
                                form.errors.reordering_point !== undefined
                            "
                            type="number"
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            placeholder="Reordering point(KG)"
                        ></JetInput>
                        <JetInputError
                            v-if="form.errors.reordering_point"
                            :message="form.errors.reordering_point"
                        />
                    </div>
                    <div class="w-full">
                        <JetLabel
                            for="maximum-shelf-life"
                            value="Maximum shelf life(days)"
                        />
                        <JetInput
                            type="number"
                            :hasError="
                                form.errors.maximum_shelf_life !== undefined
                            "
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            placeholder="Maximum Shelf Life"
                            v-model="form.maximum_shelf_life"
                        ></JetInput>
                        <JetInputError
                            v-if="form.errors.maximum_shelf_life"
                            :message="form.errors.maximum_shelf_life"
                        />
                    </div>
                </div>
                <div
                    class="flex flex-col md:flex-row w-full md:gap-8 gap-2 mt-4"
                >
                    <div class="w-full">
                        <JetLabel for="Type" value="Type" />
                        <select
                            v-model="form.type"
                            :class="{
                                'border-2 border-gray-300 dark:border-gray-700':
                                    form.errors.type !== undefined,
                                'border-red-500': form.errors.type,
                            }"
                            class="rounded-2xl px-4 py-3 text-gray-600 border-2 border-gray-300 dark:border-gray-700 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected value="">Choose a type</option>
                            <option value="pork">Pork</option>
                            <option value="beef">Beef</option>
                            <option value="chicken">Chicken</option>
                        </select>
                        <JetInputError
                            v-if="form.errors.type"
                            :message="form.errors.type"
                        />
                    </div>
                    <div class="w-full"></div>
                </div>
                <div class="flex justify-end mt-8">
                    <JetButton type="submit" :disabled="form.processing"
                        >SAVE</JetButton
                    >
                </div>
            </div>
        </form>
    </table-layout>
</template>

<script>
import TableLayout from "@/Layouts/TableLayout.vue";
import { Head, useForm, Inertia } from "@inertiajs/inertia-vue3";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import JetInputError from "@/Jetstream/InputError.vue";

export default {
    components: {
        TableLayout,
        Head,
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors,
        JetInputError,
    },
    props: {
        branches: {
            type: Array,
            default: () => [],
        },
    },
    data: () => {
        return {
            isShow: false,
            form: useForm({
                name: "",
                price: "",
                reordering_point: "50",
                maximum_shelf_life: "4",
                type: "",
            }),
        };
    },
    methods: {
        submit() {
            this.form.post(route("products.store"), {
                data: this.form,
            });
        },
    },
};
</script>

<style></style>
