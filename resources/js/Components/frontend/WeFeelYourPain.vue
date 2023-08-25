<script setup>
const complaints = [
    {
        avatar: '/images/avatars/annoyed1.png',
        text: 'Everyone waits for me to leave before suddenly tagging on their lunch order.',
    },
    {
        avatar: '/images/avatars/annoyed2.jpeg',
        text: 'I never seem to remember: Did they already pay me back? Was it in cash?',
    },
    {
        avatar: '/images/avatars/annoyed3.png',
        text: 'I feel like I\'m always the one organizing lunch for everyone.',
    },
];

const messages = [
    {
        avatar: '/images/avatars/jennis.jpeg',
        name: 'Jennis',
        text: 'I’m going to grab some Lunch',
    },
    {
        avatar: '/images/avatars/robin.jpeg',
        name: 'Robin',
        text: 'Where are you going? Could you pick up something for me too?',
    },
    {
        avatar: '/images/avatars/kim.jpeg',
        name: 'Kim',
        text: 'Can you get me a sandwich, please? thanks!',
    },
];
</script>

<template>
    <div class="py-24 lg:pb-32">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="relative isolate overflow-hidden bg-gray-900 px-6 py-8 rounded-3xl sm:px-10 sm:py-24 lg:py-24 xl:px-24">
                <div class="mx-auto max-w-7xl lg:px-8">
                    <div class="mx-auto max-w-xl text-center">
                        <h2 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">You have better things to do...</h2>
                        <p class="mt-6 text-lg leading-8 text-gray-300">than figuring out whose turn it is to fetch lunch</p>
                    </div>
                    <div class="mx-auto flow-root max-w-2xl lg:mt-20 lg:mx-0 lg:max-w-none">
                        <div class="grid sm:-mx-4 sm:grid-cols-1 sm:text-[0] lg:grid-cols-3">

                            <div
                                v-for="(complaint, key) in complaints"
                                :key="key"
                                class="sm:w-full sm:px-4 text-center mt-24 lg:mt-4"
                            >
                                <figure class="rounded-2xl h-full bg-white/5 p-8 text-sm leading-6">
                                    <figcaption class="flex justify-center items-center gap-x-4 -mt-20">
                                        <div class="ring-4  ring-white/10 rounded-full">
                                            <img
                                                :src="complaint.avatar"
                                                class="h-24 w-24 rounded-full bg-gray-50 object-cover"
                                            >
                                        </div>
                                    </figcaption>
                                    <blockquote class="mt-6 text-white">
                                        <p>“{{ complaint.text }}”</p>
                                    </blockquote>
                                </figure>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="mt-12 lg:mt-24 mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-8 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2 lg:items-center lg:gap-y-0">
                    <div class="lg:row-start-2 lg:max-w-md">
                        <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">We feel your pain</h2>
                        <p class="mt-6 text-lg leading-8 text-gray-300">At Libaro we felt the same way. Picture this scenario that played out nearly every day, just minutes before lunch break:</p>
                    </div>
                    <img src="https://images.unsplash.com/photo-1565312920580-4e604d7c3ee6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwxfDB8MXxyYW5kb218MHx8fHx8fHx8MTY4NTA0MTA0NA&ixlib=rb-4.0.3&q=80&utm_campaign=api-credit&utm_medium=referral&utm_source=unsplash_source&w=1080"
                         alt="Product screenshot"
                         class="hidden lg:block relative -z-20 min-w-h max-w-full rounded-xl shadow-xl lg:row-span-4 lg:h-[64rem] lg:max-w-none"
                    >
                    <div class="max-w-xl lg:row-start-3 lg:mt-10 lg:max-w-md lg:border-t lg:border-white/10 lg:pt-10">
                        <dl class="max-w-xl space-y-8 text-base leading-7 text-gray-300 lg:max-w-none">

                            <div
                                v-for="(message, key) in messages"
                                :key="key"
                                class="relative flex items-center gap-x-4"
                            >
                                <img :src="message.avatar" alt="" class="h-10 w-10 rounded-full bg-gray-50">
                                <div class="text-sm leading-6">
                                    <p class="font-semibold text-white">
                                        <a href="#">
                                            <span class="absolute inset-0"></span>
                                            {{ message.name }}
                                        </a>
                                    </p>
                                    <p class="text-gray-400">"{{ message.text }}"</p>
                                </div>
                            </div>
                        </dl>

                        <div class="max-w-xl lg:row-start-3 lg:mt-10 lg:max-w-md lg:border-t lg:border-white/10 pt-8">
                            <p class="text-lg leading-8 text-gray-300">As a result, Jennis was always the one tasked with fetching lunch.</p>
                        </div>
                        <div class="mt-8 flex items-centergap-x-6">
                            <a :href="route('company.register')" class="rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                                Get started for free</a>
                        </div>
                    </div>
                </div>
                <div class="pointer-events-none absolute left-12 top-1/2 -z-10 -translate-y-1/2 transform-gpu blur-3xl lg:bottom-[-12rem] lg:top-auto lg:translate-y-0 lg:transform-gpu" aria-hidden="true">
                    <div class="aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-25" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
                </div>
            </div>
        </div>
    </div>
</template>
