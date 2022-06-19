<template>
    <table-layout
        title="Users"
        note="You can add new admin or multiple user on a specific branch, pencil icon to edit and trash icon to delete the user."
    >
        <Head title="USERS" />
        <div class="flex justify-end">
            <Link
                :href="route('users.create')"
                data-tooltip-target="tooltip-create-user"
            >
                <button class="bg-green-500 text-white p-2 rounded-full">
                    <PlusIcon class="w-4 h-4" />
                </button>
            </Link>
            <tooltip id="tooltip-create-user" label="Create New User"></tooltip>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Role</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Branch</th>
                        <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Action</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <th
                            scope="row"
                            class="px-6 py-1 font-medium text-gray-900 dark:text-white whitespace-nowrap"
                        >
                            <!-- avatar -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <img
                                        class="h-8 w-8 rounded-full object-cover dark:text-gray-600"
                                        :src="user.profile_photo_url"
                                        :alt="user.name"
                                    />
                                </div>
                                <div class="ml-4">
                                    <div
                                        class="text-sm leading-5 font-medium text-gray-900 dark:text-white"
                                    >
                                        <p
                                            class="text-gray-900 dark:text-white"
                                        >
                                            {{ user.name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <td class="px-6 py-4">
                            {{ user.role == 1 ? "Admin" : "User" }}
                        </td>
                        <td class="px-6 py-4">{{ user.email }}</td>
                        <td class="px-6 py-4">
                            {{ user.branch ? user.branch.name : "-" }}
                        </td>
                        <td class="flex items-center gap-4 py-4 pr-4">
                            <Link
                                :href="route('users.edit', { id: user.id })"
                                class="font-medium text-blue-600 dark:text-blue-500"
                                ><PencilIcon class="w-4 h-4"
                            /></Link>

                            <button
                                @click.prevent="openConfirmation(user.id)"
                                class="font-medium text-red-600 dark:text-red-500 hover:underline"
                            >
                                <TrashIcon class="w-4 h-4"></TrashIcon>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <jet-confirmation-modal :show="isShow">
            <template #title>
                <h1 class="font-medium">
                    Are you sure you want to delete this user ?
                </h1>
            </template>
            <template #content>
                You are about to delete this user. This action cannot be undone.
            </template>
            <template #footer>
                <button
                    @click="isShow = false"
                    class="border-2 border-red-500 hover:bg-red-500 hover:text-white text-red-500 font-medium rounded-md px-2 mr-2"
                >
                    Cancel
                </button>
                <jet-button @click.prevent="destroy()">OK</jet-button>
            </template>
        </jet-confirmation-modal>
    </table-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import TableLayout from "@/Layouts/TableLayout.vue";
import { PencilIcon, TrashIcon } from "@heroicons/vue/outline";
import { PlusIcon } from "@heroicons/vue/solid";
import Tooltip from "@/Components/Tooltip.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetButton from "@/Jetstream/Button.vue";

export default {
    components: {
        AppLayout,
        TableLayout,
        Head,
        Link,
        PencilIcon,
        TrashIcon,
        PlusIcon,
        Tooltip,
        JetConfirmationModal,
        JetButton,
    },
    data() {
        return {
            isShow: false,
            id: null,
        };
    },
    props: {
        users: {
            type: Array,
            default: () => [],
        },
    },
    methods: {
        openConfirmation(id) {
            this.isShow = true;
            this.id = id;
        },
        destroy() {
            this.isShow = false;
            this.$inertia.delete(
                route("users.destroy", {
                    id: this.id,
                })
            );
        },
    },
};
</script>

<style></style>
