<script setup lang="ts">
defineOptions({inheritAttrs: false});

defineProps(['value']);
</script>
<template>
    <section class="component-ui-label-component">
        <label class="field-label" v-bind="$attrs">
            <span v-if="value">{{ value }}</span>
            <span v-else><slot /></span>
        </label>
    </section>
</template>
<style scoped>
@import "@css/components/ui/label-component.css";
</style>