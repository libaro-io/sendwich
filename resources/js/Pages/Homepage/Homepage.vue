<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Navigation from './sections/Navigation.vue';
import Hero from './sections/Hero.vue';
import Complaints from './sections/Complaints.vue';
import OrderTicket from './sections/OrderTicket.vue';
import Perks from './sections/Perks.vue';
import Steps from './sections/Steps.vue';
import CallToAction from './sections/CallToAction.vue';
import Footer from './sections/Footer.vue';
</script>

<template>
    <Head title="Sendwich — Get Yourself Served" />

    <div id="page-homepage">
        <div class="grain" aria-hidden="true" />
        <Navigation />
        <Hero />
        <Complaints />
        <OrderTicket />
        <Perks />
        <Steps />
        <CallToAction />
        <Footer />
    </div>
</template>

<style scoped>
@import "@css/pages/homepage.css";
</style>
