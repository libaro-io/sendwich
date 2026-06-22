<script setup lang="ts">
defineProps<{
    items?: unknown[];
    title: string;
}>();
</script>
<template>
    <div class="panel">
        <div class="list-card__head">
            <h1 class="list-card__title">{{ title }}</h1>
            <div>
                <slot name="actions"></slot>
            </div>
        </div>
        <slot name="main"></slot>
    </div>
</template>

<style scoped>
@import "@css/pages/store/sections/list.css";
</style>
