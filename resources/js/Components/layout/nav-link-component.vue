<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() => (props.active ? 'nav-link-active' : 'nav-link'));

</script>
<template>
        <Link :href="href" :class="classes">
            <slot />
        </Link>
</template>
<style scoped>
@import "@css/components/layout/nav-link-component.css";
</style>
