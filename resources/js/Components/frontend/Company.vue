<template>
    <div id="features" class="relative mb-10">
        <div class="mx-auto max-w-7xl lg:flex lg:justify-between lg:px-8 xl:justify-end">
            <div class="m-20">
                    <img class="rounded-3xl shadow-2xl w-[76rem]" src="/images/company.png" alt="">
            </div>
            <div class="px-6 lg:contents">
                <div
                    class="mx-auto max-w-2xl pb-24 pt-16 sm:pb-32 sm:pt-20 lg:ml-8 lg:mr-0 lg:w-full lg:max-w-lg lg:flex-none xl:w-1/2">
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-r dark:from-purple-400 dark:to-pink-600">Manage your company</h2>
                    <p class="mt-6 text-xl leading-8 text-gray-700 dark:text-white">A simple and intuitive way of managing your friends or colleagues</p>
                    <div class="mt-10 max-w-xl text-base leading-7 text-gray-700 lg:max-w-none">
                        <ul role="list" class="mt-8 space-y-8 text-gray-600 dark:text-white">
                            <li class="flex gap-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 h-5 w-5 flex-none text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Invite and manage your coworkers to get the party started</p>
                            </li>
                            <li class="flex gap-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 h-5 w-5 flex-none text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Settle balances with clear payout instructions.</p>
                            </li>
                            <li class="flex gap-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 h-5 w-5 flex-none text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Add or remove collegues to keep the sheet clean.</p>
                            </li>
                        </ul>
                        <div class="mt-10 flex items-centergap-x-6">
                            <a :href="route('company.register')" class="btn btn-info hover:bg-info/90">You really should get started at this point</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
</script>
