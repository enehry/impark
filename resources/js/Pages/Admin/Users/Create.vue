<template>
    <table-layout
        title="Create User"
        note="Selecting user as role will only enable branch, admin has no branch in default."
    >
        <Head title="Create User" />
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
                        <JetLabel for="user-name" value="Name" />
                        <JetInput
                            v-model="form.name"
                            :hasError="form.errors.name !== undefined"
                            type="text"
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            id="user-name"
                            required
                            autofocus
                            placeholder="Juan Dela Cruz"
                        ></JetInput>
                        <JetInputError
                            v-if="form.errors.name"
                            :message="form.errors.name"
                        />
                    </div>
                    <div class="w-full">
                        <JetLabel for="Email" value="Email" />
                        <JetInput
                            v-model="form.email"
                            :hasError="form.errors.email !== undefined"
                            type="email"
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            placeholder="Email"
                            required
                        ></JetInput>
                        <JetInputError
                            v-if="form.errors.email"
                            :message="form.errors.email"
                        />
                    </div>
                </div>
                <div
                    class="flex flex-col md:flex-row w-full md:gap-8 gap-2 mt-4"
                >
                    <div class="w-full">
                        <JetLabel for="password" value="Password" />
                        <JetInput
                            v-model="form.password"
                            :hasError="form.errors.password !== undefined"
                            type="password"
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            placeholder="Password"
                        ></JetInput>
                        <JetInputError
                            v-if="form.errors.password"
                            :message="form.errors.password"
                        />
                    </div>
                    <div class="w-full">
                        <JetLabel
                            for="retype-password"
                            value="Retype Password"
                        />
                        <JetInput
                            type="password"
                            :hasError="
                                form.errors.password_confirmation !== undefined
                            "
                            class="w-full px-4 py-2 rounded-2xl border-2 border-gray-300 dark:border-gray-700"
                            placeholder="Retype password"
                            v-model="form.password_confirmation"
                        ></JetInput>
                        <JetInputError
                            v-if="form.errors.password_confirmation"
                            :message="form.errors.password_confirmation"
                        />
                    </div>
                </div>
                <div
                    class="flex flex-col md:flex-row w-full md:gap-8 gap-2 mt-4"
                >
                    <div class="w-full">
                        <JetLabel for="Role" value="Role" />
                        <select
                            v-model="form.role"
                            :class="{
                                'border-2 border-gray-300 dark:border-gray-700':
                                    form.errors.role !== undefined,
                                'border-red-500': form.errors.role,
                            }"
                            class="rounded-2xl px-4 py-3 text-gray-600 border-2 border-gray-300 dark:border-gray-700 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected value="">Choose a role</option>
                            <option value="1">Admin</option>
                            <option value="2">User</option>
                        </select>
                        <JetInputError
                            v-if="form.errors.role"
                            :message="form.errors.role"
                        />
                    </div>
                    <div class="w-full">
                        <JetLabel for="branch" value="Branch" />
                        <select
                            :disabled="form.role == 1 || form.role == ''"
                            v-model="form.branch_id"
                            :class="{
                                'border-2 border-gray-300 dark:border-gray-700':
                                    form.errors.branch_id !== undefined,
                                'border-red-500': form.errors.branch_id,
                            }"
                            class="rounded-2xl px-4 py-3 text-gray-600 border-2 border-gray-300 dark:border-gray-700 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option selected value="">Choose a branch</option>
                            <option
                                v-for="branch in branches"
                                :value="branch.id"
                            >
                                {{ branch.name }}
                            </option>
                        </select>
                        <JetInputError
                            v-if="form.errors.branch_id"
                            :message="form.errors.branch_id"
                        />
                    </div>
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
                role: "",
                email: "",
                branch_id: "",
                password: "",
                password_confirmation: "",
            }),
        };
    },
    methods: {
        submit() {
            this.form.post(route("users.store"), {
                data: this.form,
            });
        },
    },
};
</script>

<style></style>
