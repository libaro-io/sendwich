<script setup>
import Navigation from '@/Components/frontend/Navigation.vue';
import Footer from '@/Components/frontend/Footer.vue';
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
    <main
        class="mx-auto max-w-7xl px-6 pb-24 sm:pb-32 lg:flex lg:py-40 lg:px-8 flex-col">
        <h1 class="mx-auto block font-grotesk font-bold text-7xl text-white mb-12">Cookie Policy</h1>
        <h2 class="mt-8 text-3xl font-bold tracking-tight sm:text-4xl dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-r dark:from-purple-400 dark:to-pink-600">
            General</h2>
        <p>We use cookies to collect information about how visitors use our websites. This allows
            we you
            provide the best possible experience, continue to improve our website and provide you with offers that match
            your
            interests. By clicking 'Accept' you agree to the use of these cookies. You can at
            all
            withdraw your consent at any time.</p>

        <h2 class="mt-8 text-3xl font-bold tracking-tight sm:text-4xl dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-r dark:from-purple-400 dark:to-pink-600">
            What are cookies?</h2>
        <p>Cookies and web storage technologies such as local and session storage ("cookies") make use of our
            website
            easier without having to put in extra effort. For example, they are small text files
            That
            downloaded to your device by your browser when you visit our website.</p>
    </main>
    <Footer></Footer>

</template>

<style>
p {
    @apply text-white;
}
</style>
