<script setup>

import Hero from "@/Components/frontend/Hero.vue";
import Navigation from '@/Components/frontend/Navigation.vue';
import WeFeelYourPain from '@/Components/frontend/WeFeelYourPain.vue';
import Steps from '@/Components/frontend/Steps.vue';
import Footer from "@/Components/frontend/Footer.vue";
import Benefits from "@/Components/frontend/Benefits.vue";

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
    <WeFeelYourPain></WeFeelYourPain>
    <Benefits></Benefits>
    <Steps></Steps>
    <Footer></Footer>
</template>
