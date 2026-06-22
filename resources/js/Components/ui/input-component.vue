<script setup lang="ts">
import {ref} from 'vue';

defineOptions({inheritAttrs: false});
defineProps(['modelValue']);

const emit = defineEmits(['update:modelValue']);
const input = ref<HTMLInputElement | null>(null);
const focus = () => input.value?.focus();

defineExpose({focus});
</script>
<template>
    <section class="component-ui-input-component">
        <input
            ref="input"
            class="field-input"
            v-bind="$attrs"
            :value="modelValue"
            @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        >
    </section>
</template>
<style scoped>
@import "@css/components/ui/input-component.css";
</style>
