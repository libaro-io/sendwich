<script setup lang="ts">
</script>

<template>
    <section class="homepage-cta">
        <div class="cta-band">
            <span class="cta-spark" aria-hidden="true">✦</span>
            <span class="cta-eyebrow">No, really</span>
            <h2 class="cta-title">Ready to get served?</h2>
            <p class="cta-sub">Hang up your apron. Let Sendwich handle lunch from today.</p>
            <a :href="route('company.register')" class="chunk chunk--cream chunk--lg">Get started for free</a>
        </div>
    </section>
</template>

<style scoped>
@import "@css/pages/homepage/sections/call-to-action.css";
</style>
