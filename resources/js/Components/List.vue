<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ title }}</h3>
            <div class="mt-5">
               <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Checkbox from '@/Components/Checkbox.vue';
import {useToast} from "vue-toastification";

const toast = useToast();

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
        add() {
            axios.post('/api/store/add', {
                store: this.store,
            }).then(response => {
                toast.success(response.data.message);
                this.emitter.emit('updateStores');
            }).catch(error => {
                console.error(error);
            });
        },


    }
}
</script>
