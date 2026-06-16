<script setup lang="ts">
import {Link} from '@inertiajs/vue3';

defineOptions({inheritAttrs: false});
</script>
<template>
    <section class="component-ui-dropdown-link-component">
        <Link class="dropdown-link" v-bind="$attrs">
            <slot />
        </Link>
    </section>
</template>
<style scoped>
@import "@css/components/ui/dropdown-link-component.css";
</style>

