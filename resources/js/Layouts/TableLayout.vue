<template>
    <app-layout>
        <div class="mx-auto max-w-4xl">
            <header-back-btn
                :title="title"
                :routeName="backRoute"
                :note="note"
            />
            <div class="w-full h-full">
                <slot></slot>
            </div>
        </div>
    </app-layout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import HeaderBackBtn from "@/Components/HeaderWithBack.vue";
defineProps({
    title: {
        type: String,
        default: "",
    },
    backRoute: {
        type: String,
    },
    note: String,
});
</script>

<style></style>
