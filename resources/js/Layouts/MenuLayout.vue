<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import HeaderBackBtn from "@/Components/HeaderWithBack.vue";
import HeaderOnly from "../Components/HeaderOnly.vue";
defineProps({
    header: String,
    title: String,
    hasBackBtn: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <AppLayout title="Menu">
        <div class="mx-auto max-w-2xl">
            <HeaderBackBtn v-if="hasBackBtn" :title="title" />
            <HeaderOnly v-else :header="header" :title="title" />
            <div class="flex flex-col justify-center items-center pb-28">
                <slot></slot>
            </div>
        </div>
    </AppLayout>
</template>
