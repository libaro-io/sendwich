<template>
    <div class="card bg-white shadow sm:rounded-lg">
        <div class="card-body">
            <h1>{{ title }}</h1>
            <slot></slot>
        </div>
    </div>
</template>

<script>
import Checkbox from '@/Components/Checkbox.vue';

export default {
    name: "List",
    components: {
        Checkbox,
    },
    props: {
        items: Object,
        title: String
    },
    data() {
        return {
            selectedOptions: [],
            store: Object
        };
    },
    methods: {
    }
}
</script>
