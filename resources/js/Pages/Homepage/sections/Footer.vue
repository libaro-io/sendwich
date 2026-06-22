<script setup lang="ts">
</script>

<template>
    <footer class="homepage-footer">
        <div class="foot-grid">
            <div>
                <img src="/images/logo.png" alt="Sendwich" class="foot-logo" />
                <p class="foot-by">Cooked up by <a href="https://libaro.be" target="_blank">Libaro bv</a></p>
            </div>
            <div class="foot-col">
                <span class="foot-label">Navigate</span>
                <a :href="route('home')">Home</a>
                <a :href="route('login')">Log in</a>
                <a :href="route('company.register')">Get started</a>
            </div>
            <div class="foot-col">
                <span class="foot-label">Legal</span>
                <a :href="route('legal.general')">General</a>
                <a :href="route('legal.privacy')">Privacy</a>
                <a :href="route('legal.cookies')">Cookies</a>
            </div>
        </div>
    </footer>
</template>

<style scoped>
@import "@css/pages/homepage/sections/footer.css";
</style>
