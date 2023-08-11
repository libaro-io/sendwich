<script setup>
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const features = [
    {
        icon: 'fas-fa fa-basket-shopping',
        title: 'Pick and choose',
        text: 'An intuitive and simple interface to browse and order lunch options',
    },
    {
        icon: 'fas-fa fa-clock',
        title: 'Smart Scheduling',
        text: 'A fair distribution of lunch fetching duties',
    },
    {
        icon: 'fas-fa fa-shield',
        title: 'No surprises',
        text: 'Reliable lunch times as people no longer need to wait for late orders',
    },
];
</script>

<template>
    <div class="overflow-hidden py-8 lg:py-24">
        <div class="relative isolate overflow-hidden lg:px-6 xl:px-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:mx-0">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">There is a better way</h2>
                    <p class="mt-6 text-lg leading-8 text-gray-300">As a software company, we decided to tackle this problem like we do most problems, with software! After a few iterations, we came up with Sendwich! And we’re happy to say that our lunch fetching problems are over.</p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-16 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">

                        <div
                            v-for="(feature, key) in features"
                            :key="key"
                            class="flex flex-col"
                        >
                            <dt class="text-base font-semibold leading-7 text-white">
                                <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-lg bg-white/10">
                                    <FontAwesomeIcon class="h-6 w-6" :icon="feature.icon" />
                                </div>
                                {{ feature.title }}
                            </dt>
                            <dd class="mt-1 flex flex-auto flex-col text-base leading-7 text-gray-300">
                                <p class="flex-auto">{{ feature.text }}</p>
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-8 flex items-centergap-x-6">
                        <a :href="route('company.register')" class="rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                            Get started for free</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
