<script setup lang="ts">

import {computed} from "vue";
import {Link} from "@inertiajs/vue3";

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() => (props.active ? 'nav-link-mobile-active' : 'nav-link-mobile'));
</script>
<template>
    <Link :href="href" class="component-layout-responsive-nav-link-component" :class="classes">
        <slot />
    </Link>
</template>
<style scoped>
@import "@css/components/layout/responsive-nav-link-component.css";
</style>
