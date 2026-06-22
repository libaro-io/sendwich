<script setup lang="ts">
import type { Complaint } from '@interfaces/homepage';
import { vReveal } from '@/Directives/reveal';

const complaints: Complaint[] = [
    { avatar: '/images/avatars/annoyed1.png', text: 'Everyone waits for me to leave, then quietly piles their order onto mine.', tint: 'teal', rot: '-3deg' },
    { avatar: '/images/avatars/annoyed2.jpeg', text: 'Did they pay me back? Was it cash? Honestly, I lose track every time.', tint: 'coral', rot: '2.5deg' },
    { avatar: '/images/avatars/annoyed3.png', text: "Somehow I'm always the one playing waiter for the whole team.", tint: 'sun', rot: '-1.5deg' },
];
</script>

<template>
    <section class="homepage-complaints">
        <div class="sec-head">
            <span class="sec-tab">Heard around the office</span>
            <h2 class="sec-title">You have better things to do...</h2>
            <p class="sec-sub">than figuring out whose turn it is to fetch lunch</p>
        </div>
        <div class="cards">
            <figure
                v-for="(c, i) in complaints"
                :key="i"
                v-reveal="i * 110"
                class="pin-card"
                :class="`tint-${c.tint}`"
                :style="{ '--r': c.rot }"
            >
                <span class="tape" aria-hidden="true" />
                <img :src="c.avatar" alt="" class="pin-av" />
                <figcaption class="pin-text">“{{ c.text }}”</figcaption>
            </figure>
        </div>
    </section>
</template>

<style scoped>
@import "@css/pages/homepage/sections/complaints.css";
</style>
