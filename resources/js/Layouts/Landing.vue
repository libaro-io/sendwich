<script setup>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 landing">
        <div class="w-full px-6 py-4 bg-white shadow-md overflow-hidden">
            <slot />
        </div>
    </div>
</template>
