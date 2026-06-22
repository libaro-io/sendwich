<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import type { Perk } from '@interfaces/homepage';
import { vReveal } from '@/Directives/reveal';

const perks: Perk[] = [
    { tint: 'teal', icon: 'fa-solid fa-utensils', title: 'Pick & Choose', tag: 'à la carte', desc: 'A bright, simple menu. Browse the local spots and order your lunch in a couple of taps.' },
    { tint: 'coral', icon: 'fa-solid fa-shield', title: 'No Surprises', tag: 'always on time', desc: 'Orders lock at a set time, so lunch turns up punctually — no more waiting on stragglers.' },
    { tint: 'sun', icon: 'fa-solid fa-clock', title: 'Smart Scheduling', tag: 'perfectly fair', desc: 'A fair rota picks each day’s runner automatically. The same hero never serves twice.' },
];
</script>

<template>
    <section id="perks" class="homepage-perks">
        <div class="sec-head">
            <span class="sec-tab">★ On today's menu ★</span>
            <h2 class="sec-title">There's a better way to be served</h2>
            <p class="sec-sub">We're a software company, so we fixed lunch with software. Three house specials:</p>
        </div>
        <div class="menu-grid">
            <article v-for="(p, i) in perks" :key="i" v-reveal="i * 110" class="perk" :class="`tint-${p.tint}`">
                <span class="perk-icon">
                    <FontAwesomeIcon :icon="p.icon" />
                </span>
                <span class="perk-tag">{{ p.tag }}</span>
                <h3 class="perk-title">{{ p.title }}</h3>
                <p class="perk-desc">{{ p.desc }}</p>
            </article>
        </div>
    </section>
</template>

<style scoped>
@import "@css/pages/homepage/sections/perks.css";
</style>
