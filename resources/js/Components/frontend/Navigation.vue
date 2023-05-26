<template>
    <div class="navbar">
        <div class="navbar-start">
            <div class="dropdown dark:text-pink-300">
                <label tabindex="0" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h8m-8 6h16"/>
                    </svg>
                </label>
                <ul tabindex="0" class="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52 dark:text-white">
                    <li><a class="dark:text-gray-900" href="#start">Start</a></li>
                    <li><a class="dark:text-gray-900" href="#about">About</a></li>
                    <li><a v-if="$page.props.auth.user" :href="route('dashboard')" class="btn btn-success my-2">Dashboard</a></li>
                    <li><a :href="route('company.register')" class="btn btn-info">Get started</a></li>

                </ul>
            </div>
            <a class="normal-case text-xl"><img class="w-48" src="/images/logo_w.png" alt=""></a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 dark:text-white">
                <li><a href="#start">Home</a></li>
                <li><a href="#about">About</a></li>
            </ul>
        </div>
        <div class="navbar-end invisible md:visible">
            <a v-if="$page.props.auth.user" :href="route('dashboard')" class="btn btn-success mr-2">Dashboard</a>
            <a v-else :href="route('login')" class="btn btn-success mr-2">Login</a>
            <a :href="route('company.register')" class="btn btn-info">Get started</a>
        </div>
    </div>
</template>

<script setup>

</script>
