<template>
    <div class="card bg-white shadow sm:rounded-lg">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h1 class="m-0">{{ title }}</h1>
                <div>
                    <slot name="actions"></slot>
                </div>
            </div>
            <slot name="main"></slot>
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
