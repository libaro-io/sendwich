<script setup>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 landing dark">
        <div class="w-full px-6 py-4 bg-gradient-to-r from-pink-300 via-purple-300 to-indigo-300 dark:bg-gradient-to-r dark:from-slate-800 dark:via-slate-900 dark:to-slate-800 shadow-md overflow-hidden">
            <slot />
        </div>
    </div>
</template>
