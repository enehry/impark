<script>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";

export default {
    components: {
        Head,
        Link,
        MenuLayout,
        JetInput,
        JetLabel,
        JetButton,
        JetValidationErrors,
    },
    props: {
        branch: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            form: useForm({
                name: this.branch.name,
                address: this.branch.address,
                description: this.branch.description,
            }),
        };
    },
    methods: {
        submit() {
            this.form.put(route("branch.update", this.branch.id), {
                onFinish: () => {
                    this.form.reset("name", "address", "description");
                    // redirect to branch.index
                },
            });
        },
    },
};
</script>
<template>
    <menu-layout title="EDIT BRANCH" :hasBackBtn="true">
        <Head title="Edit Branch" />
        <!-- Create a new branch -->
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
                        <JetLabel for="branch-name" value="Branch Name" />
                        <JetInput
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            id="branch-name"
                            required
                            autofocus
                            placeholder="Branch Name"
                        ></JetInput>
                    </div>
                    <div class="w-full">
                        <JetLabel for="Address" value="Branch Address" />
                        <JetInput
                            v-model="form.address"
                            type="text"
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            placeholder="Branch Address (Optional)"
                        ></JetInput>
                    </div>
                </div>
                <div class="w-full mb-4 mt-4">
                    <div class="w-full">
                        <JetLabel
                            for="branch-name"
                            value="Branch Description"
                        />
                        <JetInput
                            v-model="form.description"
                            type="text"
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            placeholder="Branch Description (Optional)"
                        ></JetInput>
                    </div>
                </div>
                <div class="flex justify-end mt-8">
                    <JetButton type="submit" :disabled="form.processing"
                        >UPDATE</JetButton
                    >
                </div>
            </div>
        </form>
    </menu-layout>
</template>

<style></style>
