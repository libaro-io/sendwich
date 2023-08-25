<script setup>
import { ref } from 'vue';

const isMobileMenuOpen = ref(false);

const openMobileMenu = () => {
    isMobileMenuOpen.value = true;
}

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
}
</script>

<template>
    <header class="absolute inset-x-0 top-0 z-50">
        <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="#" class="-m-1.5 p-1.5">
                    <span class="sr-only">Sendwich</span>
                    <img class="h-12 w-auto" src="/images/logo_w.png" alt="">
                </a>
            </div>
            <div
                @click="openMobileMenu"
                class="flex lg:hidden"
            >
                <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-400">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
            <div class="hidden lg:flex lg:gap-x-12 lg:items-center ">
            </div>
            <div class="hidden lg:flex lg:flex-1 lg:justify-end lg:items-center gap-x-6">
                <a :href="route('company.register')"
                   class="rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                    Get started for free
                </a>
                <a :href="route('login')" class="text-sm font-semibold leading-6 text-white">Log in <span aria-hidden="true">&rarr;</span></a>
            </div>
        </nav>

        <!-- Mobile menu, show/hide based on menu open state. -->
        <div v-if="isMobileMenuOpen" class="lg:hidden" role="dialog" aria-modal="true">
            <!-- Background backdrop, show/hide based on slide-over state. -->
            <div class="fixed inset-0 z-50"></div>
            <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-gray-900 px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-white/10">
                <div class="flex items-center justify-between">
                    <a href="#" class="-m-1.5 p-1.5">
                        <span class="sr-only">Sendwich</span>
                        <img class="h-12 w-auto" src="/images/logo_w.png" alt="">
                    </a>
                    <button @click="closeMobileMenu" type="button" class="-m-2.5 rounded-md p-2.5 text-gray-400">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/25">
                        <div class="space-y-2 py-6">
                        </div>
                        <div class="py-6">
                            <a :href="route('company.register')" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-white hover:bg-gray-800">Get started</a>
                            <a :href="route('login')" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-white hover:bg-gray-800">Log in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
