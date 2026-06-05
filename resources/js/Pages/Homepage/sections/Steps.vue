<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import type { Step } from '@interfaces/homepage';
import { vReveal } from '@/Directives/reveal';

const steps: Step[] = [
    { n: '1', tint: 'teal', title: 'Sign up', desc: 'Create your table and invite the whole team with a single click.' },
    { n: '2', tint: 'coral', title: 'Add your spot', desc: 'Pin your favourite lunch place and pop its menu on the board.' },
    { n: '3', tint: 'sun', title: 'Get served', desc: 'Everyone taps their meal — the chosen runner is notified automatically.' },
];
</script>

<template>
    <section class="homepage-steps">
        <div class="sec-head">
            <span class="sec-tab">The house rules</span>
            <h2 class="sec-title">Served in three</h2>
        </div>
        <div class="step-row">
            <template v-for="(s, i) in steps" :key="i">
                <div v-reveal="i * 110" class="step-card" :class="`tint-${s.tint}`">
                    <span class="step-n">{{ s.n }}</span>
                    <h3 class="step-title">{{ s.title }}</h3>
                    <p class="step-desc">{{ s.desc }}</p>
                </div>
                <FontAwesomeIcon v-if="i < 2" icon="fa-solid fa-arrow-right" class="arrow" />
            </template>
        </div>
    </section>
</template>

<style scoped>
@import "@css/pages/homepage/sections/steps.css";
</style>
