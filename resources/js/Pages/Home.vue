<script setup>

import {Head} from '@inertiajs/inertia-vue3';
import Hero from "@/Components/frontend/Hero.vue";
import Navigation from '@/Components/frontend/Navigation.vue';

const props = defineProps({});

</script>
<script>
import BreezeLandingLayout from '@/Layouts/Landing.vue';

export default {
    export: {BreezeLandingLayout},
    layout: BreezeLandingLayout,
}
</script>
<template>
    <Navigation></Navigation>
    <Hero></Hero>
</template>
