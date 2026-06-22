<script setup lang="ts">
import BreezeApplicationLogo from '@/Components/layout/application-logo-component.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="guest-layout brand-canvas">
        <Link :href="route('home')" class="guest-layout__brand">
            <BreezeApplicationLogo class="guest-layout__logo" />
        </Link>

        <div class="guest-layout__card panel">
            <slot />
        </div>
    </div>
</template>

<style scoped>
@import "@css/layouts/guest.css";
</style>
