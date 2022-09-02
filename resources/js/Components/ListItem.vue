<template>
    <div class="rounded-md mb-1 bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
        <div class="sm:flex sm:items-start">
            <div class="mt-3 sm:mt-0 sm:ml-4">
                <div class="text-lg font-medium text-gray-900">
                    {{ item.name }}
                </div>
            </div>
        </div>
        <a :href="route('store.show', item.id)" v-if="editable"
           class="text-white bg-blue-300 hover:vg-blue-400 rounded-md px-5 py-2">
            Edit
        </a>
    </div>
</template>

<script>
import axios from "axios";
import Checkbox from '@/Components/Checkbox.vue';
import {useToast} from "vue-toastification";

const toast = useToast();

export default {
    name: "ListItem",
    components: {
        Checkbox,
    },
    props: {
        item: Object,
        editable: Boolean
    },
    data() {
        return {};
    },
    methods: {
    }
}
</script>
