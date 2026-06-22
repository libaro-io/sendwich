<script setup lang="ts">
</script>

<template>
    <header class="homepage-nav">
        <a :href="route('home')">
            <img src="/images/logo.png" alt="Sendwich — get yourself served" class="logo" />
        </a>
        <div class="nav-right">
            <a :href="route('login')" class="navlink">Log in</a>
            <a :href="route('company.register')" class="chunk chunk--teal chunk--sm">Get started</a>
        </div>
    </header>
</template>

<style scoped>
@import "@css/pages/homepage/sections/navigation.css";
</style>
