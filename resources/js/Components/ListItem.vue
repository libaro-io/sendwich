<template>
    <div class="card bg-base-200 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between">
                <div class="mt-3 sm:mt-0 sm:ml-4">
                    <div class="text-lg font-medium text-base-content">
                        {{ item.name }}
                    </div>
                </div>
                <a :href="route( type+'.show', item.id)" v-if="editable"
                   class="btn btn-primary">
                    Edit
                </a>
            </div>

        </div>
    </div>
</template>

<script>
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
        editable: Boolean,
        type: String
    },
    data() {
        return {};
    },
    methods: {}
}
</script>
