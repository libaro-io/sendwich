<template>
    <div class="navbar">
        <div class="navbar-start">
            <div class="dropdown">
                <label tabindex="0" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h8m-8 6h16"/>
                    </svg>
                </label>
                <ul tabindex="0" class="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52 dark:text-white">
                    <li><a href="#start">Start</a></li>
                    <li><a href="#about">About</a></li>

                </ul>
            </div>
            <a class="normal-case text-xl">Sendwich</a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 dark:text-white">
                <li><a href="#start">Home</a></li>
                <li><a href="#about">About</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <a class="btn btn-info">Get started</a>
        </div>
    </div>
</template>

<script setup>

</script>
