<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import { InformationCircleIcon } from "@heroicons/vue/solid";

const confirmBypass = ref(false);
const isLoading = ref(false);

const confirm = () => {
    confirmBypass.value = true;
};

const confirmed = () => {
    isLoading.value = true;
    Inertia.visit(route("forecasting.bypass"), {
        method: "post",
        preserveState: false,
        onSuccess() {
            confirmBypass.value = false;
            isLoading.value = false;
        },
    });
};

const closeModal = () => {
    confirmBypass.value = false;
};
</script>

<template>
    <JetActionSection>
        <template #title> Bypassed Kg Per Day </template>

        <template #description>
            Increment or decrement the default kg of every branches.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-300">
                This action will decrease or increase the default kg per day of
                every branches. You can use it if you want to bypass the
                scheduled task for changing the kg per day.
            </div>

            <div class="mt-5">
                <JetDangerButton @click="confirm">
                    Increment/Decrement kg per day
                </JetDangerButton>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <JetDialogModal :show="confirmBypass" @close="closeModal">
                <template #title>
                    <div class="flex gap-2 items-center">
                        <information-circle-icon
                            class="w-5 h-5 text-amber-500"
                        />
                        Confirm Bypass
                    </div>
                </template>

                <template #content>
                    Are you sure you want to bypass the scheduled task for
                    changing the kg per day?
                </template>

                <template #footer>
                    <JetSecondaryButton @click="closeModal">
                        Cancel
                    </JetSecondaryButton>

                    <JetDangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': isLoading }"
                        :disabled="isLoading"
                        @click="confirmed"
                    >
                        Ok
                    </JetDangerButton>
                </template>
            </JetDialogModal>
        </template>
    </JetActionSection>
</template>
