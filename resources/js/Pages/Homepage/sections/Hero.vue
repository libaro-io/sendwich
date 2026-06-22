<script setup lang="ts">
// Hero artwork — a random dish is served on every page load. Add new images here.
const heroImages: string[] = [
    '/images/hero-broodje.svg',
    '/images/hero-ceasarsalad.svg',
];

// Pick a random dish, but never the same one twice in a row — with only a few
// images a plain random pick repeats half the time, which looks broken.
const pickHeroImage = (): string => {
    const previousImage = localStorage.getItem('homepage-hero-image');
    const candidates = heroImages.filter((image) => image !== previousImage);
    const image = candidates[Math.floor(Math.random() * candidates.length)] ?? heroImages[0];
    localStorage.setItem('homepage-hero-image', image);
    return image;
};
const heroImage: string = pickHeroImage();

// Smoothly glide to a section instead of jumping instantly.
const scrollTo = (id: string): void => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};
</script>

<template>
    <section class="homepage-hero">
        <div class="hero-grid">
            <div>
                <span class="kicker">Sandwiches As A Service</span>
                <span class="script">no more playing waiter</span>
                <h1 class="title">Streamline your lunch break</h1>
                <p class="sub">
                    Stuck in a lunchtime nightmare, where all eyes fixate on you, anticipating requests to fetch their meals as well? Streamline your lunch break and stop wasting your precious time today!
                </p>
                <div class="cta">
                    <a :href="route('company.register')" class="chunk chunk--teal chunk--lg">Get started — it's free →</a>
                    <a href="#perks" @click.prevent="scrollTo('perks')" class="ghost">see how it works</a>
                </div>
            </div>

            <div class="hero-art" aria-hidden="true">
                <div class="cloche-wrap">
                    <span class="steam"><i /><i /><i /></span>
                    <img :src="heroImage" alt="A freshly made lunch" class="cloche" />
                    <div class="cloche-shadow" />
                </div>
            </div>
        </div>
        <div class="checker" aria-hidden="true" />
    </section>
</template>

<style scoped>
@import "@css/pages/homepage/sections/hero.css";
</style>
