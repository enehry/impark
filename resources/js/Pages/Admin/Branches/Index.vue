<template>
    <menu-layout title="BRANCHES" :hasBackBtn="true">
        <Head title="Branches" />
        <div
            v-if="branches.length"
            class="m-auto grid md:grid-cols-3 lg:gap-20 gap-10 grid-cols-2 content-evenly"
        >
            <MenuTile
                v-for="branch in branches"
                :key="branch.id"
                :label="branch.name"
            >
                <div class="flex justify-between items-start">
                    <UserIcon class="w-12 h-12 text-white dark:text-gray-500" />
                    <div>
                        <button
                            @click.prevent="openConfirmation(branch.id)"
                            :data-tooltip-target="`tooltip-delete-${branch.id}`"
                        >
                            <TrashIcon
                                class="w-4 h-4 text-white dark:text-gray-500"
                            />
                        </button>
                        <Link
                            :href="route('branch.edit', { id: branch.id })"
                            :data-tooltip-target="`tooltip-edit-${branch.id}`"
                        >
                            <PencilIcon
                                class="w-4 h-4 text-white dark:text-gray-500"
                            />
                        </Link>
                    </div>
                    <tooltip
                        :id="`tooltip-edit-${branch.id}`"
                        :label="`Edit ${branch.name}`"
                    ></tooltip>
                    <tooltip
                        :id="`tooltip-delete-${branch.id}`"
                        :label="`Delete ${branch.name}`"
                    ></tooltip>
                </div>
            </MenuTile>
        </div>
        <Empty v-else label="Branch" />

        <div class="w-full flex justify-end mt-10 mr-16">
            <Link
                :href="route('branch.create')"
                data-tooltip-target="create-branch"
                class="bg-green-500 cursor-pointer rounded-full p-2 hover:bg-green-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                type="button"
            >
                <plus-icon class="w-5 h-5 text-white"></plus-icon>
            </Link>

            <tooltip id="create-branch" label="Create New Branch"></tooltip>
        </div>
        <jet-confirmation-modal :show="isShow">
            <template #title>
                <h1 class="font-medium">
                    Are you sure you want to delete this branch ?
                </h1>
            </template>
            <template #content>
                You are about to delete this branch. This action cannot be
                undone.
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
    </menu-layout>
</template>

<script>
import { Head, Link, Inertia } from "@inertiajs/inertia-vue3";
import ActionModal from "@/Components/ActionModal.vue";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import MenuTile from "@/Components/MenuTile.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetButton from "@/Jetstream/Button.vue";
import {
    UserIcon,
    PlusIcon,
    TrashIcon,
    PencilIcon,
} from "@heroicons/vue/outline";
import SmallModal from "@/Components/SmallModal.vue";
import JetModal from "@/Jetstream/Modal.vue";
import Tooltip from "@/Components/Tooltip.vue";
import Empty from "@/Components/Empty.vue";

export default {
    components: {
        Head,
        Link,
        MenuLayout,
        MenuTile,
        UserIcon,
        PlusIcon,
        TrashIcon,
        SmallModal,
        JetModal,
        Tooltip,
        Empty,
        PencilIcon,
        ActionModal,
        JetConfirmationModal,
        JetButton,
    },
    data() {
        return {
            isShow: false,
            idToDelete: null,
        };
    },
    props: {
        branches: {
            type: Array,
            default: () => [],
        },
    },
    methods: {
        destroy() {
            if (this.idToDelete) {
                this.$inertia.delete(
                    route("branch.destroy", { id: this.idToDelete })
                );

                this.isShow = false;
            }
        },
        openConfirmation(id) {
            this.idToDelete = id;
            this.isShow = true;
        },
    },
    mounted() {},
};
// }
// defineProps({
//     branches: {
//         type: Array,
//         default: () => [],
//     },
// });

// const show = ref(false);

// const openModal = () => {
//     show.value = true;
// };

// const closeModal = () => {
//     show.value = false;
// };

// const destroy = ($id) => {
//     if (confirm("Are you sure you want to delete this branch?")) {
//         Inertia.post(route("branch.destroy", $id));
//     }
// };
</script>

<style></style>
