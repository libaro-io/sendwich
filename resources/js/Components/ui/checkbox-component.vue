<script setup lang="ts">
import {computed} from 'vue';

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: null,
    },
});

const emit = defineEmits(['update:checked']);

const proxyChecked = computed({
    get: () => props.checked,
    set: (value) => emit('update:checked', value),
});
</script>
<template>
    <section class="component-ui-checkbox-component">
        <input type="checkbox" :value="value" v-model="proxyChecked" class="field-checkbox">
    </section>
</template>
