<script setup lang="ts">
import { useToast } from "vue-toastification";
import { usePage } from "@inertiajs/vue3";
import { watch } from "vue";

const toast = useToast();
const page = usePage();

watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) {
            toast.success(flash.success);
        }
    },
    { deep: true }
);
</script>

<template>
    <section class="component-layout-flash-messages-component">
    </section>
</template>

<style scoped>
@import "@css/components/layout/flash-messages-component.css";
</style>
