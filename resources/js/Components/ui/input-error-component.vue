<script setup lang="ts">
defineProps(['message']);
</script>
<template>
    <section v-show="message" class="component-ui-input-error-component">
        <p class="field-error">
            {{ message }}
        </p>
    </section>
</template>
<style scoped>
@import "@css/components/ui/input-error-component.css";
</style>
