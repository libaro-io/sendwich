<script setup lang="ts">
import BreezeApplicationLogo from '@/Components/layout/application-logo-component.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';

// Safari keeps pages in the back/forward cache even with Cache-Control:
// no-store, so a restored guest page must revalidate against the server —
// the guest middleware then redirects authenticated users away.
const reloadWhenRestoredFromBackForwardCache = (event: PageTransitionEvent): void => {
    if (event.persisted) {
        window.location.reload();
    }
};

onMounted(() => window.addEventListener('pageshow', reloadWhenRestoredFromBackForwardCache));
onUnmounted(() => window.removeEventListener('pageshow', reloadWhenRestoredFromBackForwardCache));
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
