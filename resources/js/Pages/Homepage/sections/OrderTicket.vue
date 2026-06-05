<script setup lang="ts">
import type { OrderLine } from '@interfaces/homepage';
import { vReveal } from '@/Directives/reveal';

const order: OrderLine[] = [
    { avatar: '/images/avatars/jennis.jpeg', name: 'Jennis', text: "I'm popping out to grab some lunch.", note: '' },
    { avatar: '/images/avatars/robin.jpeg', name: 'Robin', text: 'Ooh, where to? Grab me something too?', note: '+1' },
    { avatar: '/images/avatars/kim.jpeg', name: 'Kim', text: 'One sandwich for me please — you legend!', note: '+1' },
];
</script>

<template>
    <section class="homepage-order-ticket">
        <div class="scene-grid">
            <div>
                <span class="sec-tab sec-tab--teal">We feel your pain</span>
                <h2 class="scene-title">It always starts so innocently…</h2>
                <p class="scene-lede">
                    Then, minutes before the break, the same little chat plays out — and the same
                    person always ends up walking out the door with a list.
                </p>
                <p class="scene-punch">So Jennis became everyone's waiter. Every single day.</p>
            </div>
            <div class="ticket" v-reveal>
                <div class="ticket-head">
                    <span class="ticket-dot" /> Table 4 · today's order
                </div>
                <ul class="order-list">
                    <li v-for="(m, i) in order" :key="i" class="order-row">
                        <img :src="m.avatar" alt="" class="order-av" />
                        <div class="order-body">
                            <span class="order-name">{{ m.name }}</span>
                            <span class="order-text">{{ m.text }}</span>
                        </div>
                        <span v-if="m.note" class="order-note">{{ m.note }}</span>
                    </li>
                </ul>
                <div class="ticket-foot">Server <strong>Jennis</strong> · party of 3</div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@import "@css/pages/homepage/sections/order-ticket.css";
</style>
