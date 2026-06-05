<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Navigation from '@/Pages/Homepage/sections/Navigation.vue';
import Hero from '@/Pages/Homepage/sections/Hero.vue';
import Complaints from '@/Pages/Homepage/sections/Complaints.vue';
import OrderTicket from '@/Pages/Homepage/sections/OrderTicket.vue';
import Perks from '@/Pages/Homepage/sections/Perks.vue';
import Steps from '@/Pages/Homepage/sections/Steps.vue';
import CallToAction from '@/Pages/Homepage/sections/CallToAction.vue';
import Footer from '@/Pages/Homepage/sections/Footer.vue';
</script>

<template>
    <Head title="Sendwich — Get Yourself Served">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link
            href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Caveat:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

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
