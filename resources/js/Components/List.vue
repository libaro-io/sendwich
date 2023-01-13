<template>
    <div class="card bg-white shadow-xl">
        <div class="card-body">
            <h3 class="card title">{{ title }}</h3>
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
