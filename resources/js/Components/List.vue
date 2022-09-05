<template>
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="card title">{{ title }}</h3>
            <slot></slot>
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
