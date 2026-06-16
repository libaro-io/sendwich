<script setup>
import { ref, watch } from 'vue';
import BreezeApplicationLogo from '@/Components/layout/application-logo-component.vue';
import BreezeDropdown from '@/Components/ui/dropdown-component.vue';
import BreezeDropdownLink from '@/Components/ui/dropdown-link-component.vue';
import BreezeNavLink from '@/Components/layout/nav-link-component.vue';
import BreezeResponsiveNavLink from '@/Components/layout/responsive-nav-link-component.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import FlashMessages from "@/Components/layout/flash-messages-component.vue";
import Footer from "@/Components/frontend/footer-component.vue";

const page = usePage();

watch(page.props, (value) => {
    window.Laravel.jsPermissions = JSON.parse(value.js_permissions ?? null);
}, {
    deep: true,
    immediate: true,
});

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="auth-layout brand-canvas">
            <div>
                <nav class="auth-nav">
                    <div class="auth-nav__container">
                        <div class="auth-nav__row">
                            <div class="auth-nav__left">
                                <!-- Logo -->
                                <div class="auth-nav__brand">
                                    <Link :href="route('dashboard')">
                                        <BreezeApplicationLogo class="auth-nav__logo" />
                                    </Link>
                                </div>

                                <!-- Navigation Links -->
                                <div class="auth-nav__links">
                                    <BreezeNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                        Today
                                    </BreezeNavLink>
                                    <BreezeNavLink :href="route('history')" :active="route().current('history')">
                                        History
                                    </BreezeNavLink>
                                    <BreezeNavLink v-if="can('edit-store')" :href="route('store.index')" :active="route().current('store.index')">
                                        Menu
                                    </BreezeNavLink>
                                    <BreezeNavLink v-if="can('edit-company')" :href="route('company.show')" :active="route().current('company.show')">
                                        Users
                                    </BreezeNavLink>
                                    <BreezeNavLink v-if="can('edit-company')" :href="route('settings.show')" :active="route().current('settings.show')">
                                        Settings
                                    </BreezeNavLink>
                                </div>
                            </div>

                            <div class="auth-nav__user">
                                <!-- Settings Dropdown -->
                                <div class="auth-nav__dropdown">
                                    <BreezeDropdown align="right" width="48">
                                        <template #trigger>
                                            <span class="auth-nav__trigger-wrap">
                                                <button type="button" class="auth-nav__trigger">
                                                    {{ $page.props.auth.user.name }}
                                                    <FontAwesomeIcon icon="fa-solid fa-chevron-down" class="auth-nav__trigger-icon" />
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <BreezeDropdownLink :href="route('logout')" method="post" as="button">
                                                Log Out
                                            </BreezeDropdownLink>
                                        </template>
                                    </BreezeDropdown>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="auth-nav__hamburger">
                                <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="auth-nav__icon-btn">
                                    <FontAwesomeIcon :icon="showingNavigationDropdown ? 'fa-solid fa-xmark' : 'fa-solid fa-bars'" class="auth-nav__icon" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div class="auth-nav__mobile" :class="showingNavigationDropdown ? 'auth-nav__mobile--open' : 'auth-nav__mobile--closed'">
                        <div class="auth-nav__mobile-links">
                            <BreezeResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Today
                            </BreezeResponsiveNavLink>
                            <BreezeResponsiveNavLink :href="route('history')" :active="route().current('history')">
                                History
                            </BreezeResponsiveNavLink>
                            <BreezeResponsiveNavLink v-if="can('edit-store')" :href="route('store.index')" :active="route().current('store.index')">
                                Menu
                            </BreezeResponsiveNavLink>
                            <BreezeResponsiveNavLink v-if="can('edit-company')" :href="route('company.show')" :active="route().current('company.show')">
                                Users
                            </BreezeResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="auth-nav__mobile-user">
                            <div class="auth-nav__mobile-user-info">
                                <div class="auth-nav__mobile-user-name">{{ $page.props.auth.user.name }}</div>
                                <div class="auth-nav__mobile-user-email">{{ $page.props.auth.user.email }}</div>
                            </div>

                            <div class="auth-nav__mobile-actions">
                                <BreezeResponsiveNavLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </BreezeResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Heading -->
                <header class="auth-header" v-if="$slots.header">
                    <div class="auth-header__container">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main>
                    <FlashMessages />
                    <slot />
                </main>
            </div>

            <div>
                <Footer></Footer>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "@css/layouts/authenticated.css";
</style>
