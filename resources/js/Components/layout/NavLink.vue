<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },

    props: ['href', 'active'],

    computed: {
        classes() {
            return this.active ? 'nav-link-active' : 'nav-link';
        }
    }
}
</script>
<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
