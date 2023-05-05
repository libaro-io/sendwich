<template>
    <div class="relative isolate overflow-hidden bg-gray-900 rounded-xl">
        <svg
            class="absolute inset-0 -z-10 h-full w-full stroke-gray-200 [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)]"
            aria-hidden="true">
            <defs>
                <pattern id="0787a7c5-978c-4f66-83c7-11c213f99cb7" width="200" height="200" x="50%" y="-1"
                         patternUnits="userSpaceOnUse">
                    <path d="M.5 200V.5H200" fill="none"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" stroke-width="0" fill="url(#0787a7c5-978c-4f66-83c7-11c213f99cb7)"/>
        </svg>
        <div class="mx-auto max-w-7xl px-6 pt-10 pb-24 sm:pb-32 lg:flex flex-col lg:py-20 lg:px-8">
            <div class="lg:flex-shrink-0 lg:mx-0 lg:pt-8 mx-auto text-center">
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">Manage stores and products yourself</h2>
                <p class="text-white mt-6">Recreate the menu's from local stores so your colleagues can order there favorite sandwich.</p>
            </div>
            <div class="flex lg:flex-none flex-col lg:max-w-none lg:mr-0 lg:mt-10 max-w-2xl mt-16 mx-auto sm:mt-24">
                <div class="max-w-3xl flex-none sm:max-w-5xl lg:max-w-none">
                    <div class="-m-2 rounded-xl bg-gray-900/5 p-2 ring-1 ring-inset ring-gray-900/10 lg:-m-4 lg:rounded-2xl lg:p-4">
                        <img src="/images/stores.png"
                             alt="App screenshot" width="2432" height="1442"
                             class="w-[76rem] rounded-md shadow-2xl ring-1 ring-gray-900/10"/>
                    </div>
                </div>
                <div class="mt-10 flex items-center gap-x-6">
                    <a href="#" class="btn">You really should get started at this point</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>
