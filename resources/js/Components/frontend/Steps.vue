<script setup>
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
</script>

<template>
    <div id="steps" class="overflow-hidden py-24 sm:py-32">
        <div class="mx-auto max-w-7xl lg:px-8">
            <div class="relative isolate overflow-hidden bg-gray-900 px-6 py-8 rounded-3xl sm:px-10 sm:py-24 lg:py-8 xl:px-24">
                <div class="pt-8 mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2">
                    <div class="lg:pr-8 lg:pt-4">
                        <div class="lg:max-w-lg">
                            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Get started in 3 steps</h2>
                            <dl class="mt-10 max-w-xl space-y-8 text-base leading-7 text-gray-300 lg:max-w-none">
                                <div class="relative pl-12">
                                    <div class="absolute left-1 top-1 h-8 w-8 rounded-full flex justify-center align-middle bg-white/10">
                                        <p class="text-center text-2xl h-5 w-5 text-white m-0">3</p>
                                    </div>
                                    <dt class="inline font-semibold text-white">
                                        Sign up.
                                    </dt>
                                    <dd class="inline">Sign up and invite your colleagues through a simple click of a button.</dd>
                                </div>
                                <div class="relative pl-12">
                                    <div class="absolute left-1 top-1 h-8 w-8 rounded-full flex justify-center align-middle bg-white/10">
                                        <p class="text-center text-2xl h-5 w-5 text-white m-0">3</p>
                                    </div>
                                    <dt class="inline font-semibold text-white">
                                        Add your store.
                                    </dt>
                                    <dd class="inline">Add your favorite store along with its menu.</dd>
                                </div>
                                <div class="relative pl-12">
                                    <div class="absolute left-1 top-1 h-8 w-8 rounded-full flex justify-center align-middle bg-white/10">
                                        <p class="text-center text-2xl h-5 w-5 text-white m-0">3</p>
                                    </div>
                                    <dt class="inline font-semibold text-white">
                                        Start placing orders.
                                    </dt>
                                    <dd class="inline">Find your favorite meal in the list and click order.<br />At a predefined time, the chosen runner will receive an email asking them to go pick up the order.</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <img src="/images/lunch.jpg" alt="Product screenshot" class="w-[48rem] hidden lg:block max-w-none rounded-xl shadow-xl sm:w-[57rem] md:-ml-4 lg:-ml-0">
                </div>

                <div class="relative">
                    <div class="px-6 py-16 sm:px-6 sm:py-32 lg:px-8">
                        <div class="mx-auto max-w-2xl text-center">
                            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Start using Sendwich today</h2>
                            <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-300">And stop wasting valuable time on getting lunch every day.</p>
                            <div class="mt-10 flex items-center justify-center gap-x-6">
                                <a :href="route('company.register')" class="rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                                    et started for free</a>
                            </div>
                        </div>
                    </div>
                    <svg viewBox="0 0 1024 1024" class="absolute left-1/2 top-1/2 -z-10 h-[64rem] w-[64rem] -translate-x-1/2 [mask-image:radial-gradient(closest-side,white,transparent)]" aria-hidden="true">
                        <circle cx="512" cy="512" r="512" fill="url(#8d958450-c69f-4251-94bc-4e091a323369)" fill-opacity="0.7" />
                        <defs>
                            <radialGradient id="8d958450-c69f-4251-94bc-4e091a323369">
                                <stop stop-color="#7775D6" />
                                <stop offset="1" stop-color="#E935C1" />
                            </radialGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>
