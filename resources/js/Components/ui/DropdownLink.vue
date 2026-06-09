<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    }
}
</script>
<template>
    <Link class="dropdown-link">
        <slot />
    </Link>
</template>
