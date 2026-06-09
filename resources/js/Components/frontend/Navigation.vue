<script setup>
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const isMobileMenuOpen = ref(false);

const openMobileMenu = () => {
    isMobileMenuOpen.value = true;
}

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
}
</script>

<template>
    <header class="site-nav">
        <nav class="site-nav__bar" aria-label="Global">
            <div class="site-nav__brand-wrap">
                <a :href="route('home')" class="site-nav__brand">
                    <span class="sr-only">Sendwich</span>
                    <img class="site-nav__logo" src="/images/logo.png" alt="Sendwich Logo">
                </a>
            </div>
            <div class="site-nav__burger-wrap" @click="openMobileMenu">
                <button type="button" class="site-nav__icon-btn">
                    <span class="sr-only">Open main menu</span>
                    <FontAwesomeIcon icon="fa-solid fa-bars" class="site-nav__icon" />
                </button>
            </div>
            <div class="site-nav__actions">
                <a :href="route('login')" class="site-nav__login">Log in <span aria-hidden="true">&rarr;</span></a>
                <a :href="route('company.register')" class="chunk chunk--teal chunk--sm">
                    Get started for free
                </a>
            </div>
        </nav>

        <!-- Mobile menu -->
        <div v-if="isMobileMenuOpen" class="site-nav__mobile" role="dialog" aria-modal="true">
            <div class="site-nav__backdrop" @click="closeMobileMenu"></div>
            <div class="site-nav__panel">
                <div class="site-nav__panel-head">
                    <a :href="route('home')" class="site-nav__brand">
                        <span class="sr-only">Sendwich</span>
                        <img class="site-nav__logo" src="/images/logo.png" alt="Sendwich Logo">
                    </a>
                    <button @click="closeMobileMenu" type="button" class="site-nav__icon-btn">
                        <span class="sr-only">Close menu</span>
                        <FontAwesomeIcon icon="fa-solid fa-xmark" class="site-nav__icon" />
                    </button>
                </div>
                <div class="site-nav__panel-body">
                    <a :href="route('login')" class="site-nav__mobile-login">Log in</a>
                    <a :href="route('company.register')" class="chunk chunk--teal site-nav__mobile-cta">Get started</a>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
@import "@css/components/frontend/navigation.css";
</style>