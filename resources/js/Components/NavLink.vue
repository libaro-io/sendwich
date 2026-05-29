<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },

    props: ['href', 'active'],

    computed: {
        classes() {
            return this.active
                ? 'inline-flex items-center px-1 pt-1 border-b-2 border-accent text-sm font-bold leading-5 text-white focus:outline-none focus:border-accent transition  duration-150 ease-in-out'
                : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-gray-50 hover:text-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-100 focus:border-accent transition duration-150 ease-in-out'
        }
    }
}
</script>
<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
