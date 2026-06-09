<script setup lang="ts">
import Navigation from '@/Components/frontend/Navigation.vue';
import Footer from '@/Components/frontend/Footer.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Cookie Policy" />

    <div class="legal-page">
        <div>
            <Navigation />
            <main class="legal-page__body">
                <h1 class="legal-page__title">Cookie Policy</h1>

                <h2 class="legal-page__heading">General</h2>
                <p>We use cookies to collect information about how visitors use our websites. This allows
                    we you
                    provide the best possible experience, continue to improve our website and provide you with offers that match
                    your
                    interests. By clicking 'Accept' you agree to the use of these cookies. You can at
                    all
                    withdraw your consent at any time.</p>

                <h2 class="legal-page__heading">What are cookies?</h2>
                <p>Cookies and web storage technologies such as local and session storage ("cookies") make use of our
                    website
                    easier without having to put in extra effort. For example, they are small text files
                    That
                    downloaded to your device by your browser when you visit our website.</p>
            </main>
        </div>
        <Footer />
    </div>
</template>

<style scoped>
@import "@css/pages/legal.css";
</style>
