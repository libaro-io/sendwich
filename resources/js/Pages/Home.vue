<script setup>

import {Head} from '@inertiajs/inertia-vue3';
import Hero from "@/Components/frontend/Hero.vue";
import Navigation from '@/Components/frontend/Navigation.vue';
import Features from '@/Components/frontend/Features.vue';
import Store from '@/Components/frontend/Store.vue';
import Footer from "@/Components/frontend/Footer.vue";
import About from "@/Components/frontend/About.vue";

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
    <Store></Store>
    <About></About>
    <Features></Features>
    <Footer></Footer>
</template>
