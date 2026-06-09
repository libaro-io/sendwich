<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const errors = computed(() => usePage().props.errors);

const hasErrors = computed(() => Object.keys(errors.value).length > 0);
</script>

<template>
    <div v-if="hasErrors" class="validation-errors">
        <div class="validation-errors__title">Whoops! Something went wrong.</div>

        <ul class="validation-errors__list">
            <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
        </ul>
    </div>
</template>

<style scoped>
@import "@css/components/ui/validation-errors.css";
</style>