<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Bon'app</h3>
            <div class="mt-5">
                <div v-for="store in stores"
                     class="rounded-md mb-1 bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ store.name }}
                            </div>
                        </div>
                    </div>

                    <button
                        @click="editStore(store)"
                        class="text-gray-500-400 hover:text-gray-600 bg-white rounded-md px-5 py-2">
                     Edit
                    </button>
                </div>
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
    name: "Stores",
    components: {
        Checkbox,
    },
    props: {
        stores: Array,
    },
    data() {
        return {
            selectedOptions: [],
            store: Object
        };
    },
    methods: {
        editStore(store) {
            route('store.show', store.id )
        },
        addStore(store) {
            axios.post('/api/store/add', {
                store: store,
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
