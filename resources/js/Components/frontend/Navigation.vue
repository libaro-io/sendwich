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
                <ul tabindex="0"
                    class="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52 dark:text-white">
                    <a v-if="$page.props.auth.user" :href="route('dashboard')" class="btn btn-success my-2">Dashboard</a>
                    <a v-else :href="route('login')" class="btn btn-success my-2 hover:bg-success/90">Login</a>
                    <a :href="route('company.register')" class="btn btn-info hover:bg-info/90">Get started</a>

                </ul>
            </div>
            <a :href="route('home')" class="normal-case text-xl"><img class="w-48" src="/images/logo_w.png" alt=""></a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 dark:text-white">
            </ul>
        </div>
        <div class="navbar-end invisible md:visible">
            <div v-if="$page.props.auth.user" class="relative group">
                <div class="absolute inset-0 bg-gradient-to-tr from-success to-success/90 rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-200 animate-tilt"></div>
                <a :href="route('dashboard')" class="btn btn-success mr-2 relative">Dashboard</a>
            </div>
            <div v-else class="relative group">
                <div class="absolute inset-0 bg-gradient-to-tr from-success to-success/90 rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-200 animate-tilt"></div>
                <a :href="route('login')" class="btn btn-success mr-2 hover:bg-success/90 relative">Login</a>
            </div>
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-tr from-info to-info/90 rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-200 animate-tilt"></div>
                <a :href="route('company.register')" class="btn btn-info hover:bg-info/90 relative">Get started</a>
            </div>
        </div>
    </div>
</template>

<script setup>

</script>
