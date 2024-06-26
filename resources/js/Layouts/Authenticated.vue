<script setup>
import {onBeforeMount, onMounted, ref, watch} from 'vue';
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import BreezeDropdown from '@/Components/Dropdown.vue';
import BreezeDropdownLink from '@/Components/DropdownLink.vue';
import BreezeNavLink from '@/Components/NavLink.vue';
import BreezeResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/inertia-vue3';
import FlashMessages from "@/Components/FlashMessages.vue";
import Footer from "@/Components/frontend/Footer.vue";
import { usePage } from '@inertiajs/inertia-vue3';

const page = usePage();

watch(page.props, (value) => {
    window.Laravel.jsPermissions = JSON.parse(value.js_permissions ?? null);
}, {
    deep: true,
    immediate: true,
});

const showingNavigationDropdown = ref(false);
</script>
<style>
.bg-nav{
    background: #a8ff78;  /* fallback for old browsers */
    background: -webkit-linear-gradient(to right, #78ffd6, #7fbeab);  /* Chrome 10-25, Safari 5.1-6 */
    background: linear-gradient(to right, #78ffd6, #7fbeab); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

}
</style>
<template>
    <div>
        <div class="min-h-screen bg-rainbow flex flex-col justify-between">
            <div>
                <nav class="nav ">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                        <BreezeApplicationLogo class="block h-9 w-auto" />
                                    </Link>
                                </div>

                                <!-- Navigation Links -->
                                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
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

                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <!-- Settings Dropdown -->
                                <div class="ml-3 relative">
                                    <BreezeDropdown align="right" width="48">
                                        <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
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
                            <div class="-mr-2 flex items-center sm:hidden">
                                <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                        <div class="pt-2 pb-3 space-y-1">
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
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="px-4">
                                <div class="font-medium text-base text-gray-800">{{ $page.props.auth.user.name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <BreezeResponsiveNavLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </BreezeResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                </nav>
                <!-- Page Heading -->
                <header class="bg-white shadow" v-if="$slots.header">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
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
