<template>
    <div class="bg-white shadow sm:rounded-md">
        <div class="px-4 py-5 sm:p-6">
            <h2>Menu</h2>
            <div v-if="stores" class="mt-5 flex flex-col gap-2">
                <div v-for="(store , index) in stores"
                     :key="index"
                     class="card card-compact bg-gray-50 shadow">
                    <div class="card-body flex justify-between align-middle">
                        <div class="flex flex-end flex-row justify-between sm:flex sm:items-start">
                            <div class="mt-3 sm:mt-0 ">
                                <div class="text-sm font-medium text-gray-900">{{ store.name }}</div>
                                <p class="mt-1" v-if="store.order_count">Orders: {{ store.order_count }}</p>
                                <div class="mt-1 sm:flex sm:items-center ">
                                    <!--                <p class="text-primary font-bold">{{ product.store.ordercount }}</p>-->
                                </div>
                            </div>
                            <div class="card-actions justify-end mt-2">
                                <label
                                    @click="$emit('selectStore', store)"
                                    :for="'option-modal-'+store.id"
                                    class="btn btn-sm btn-success modal-button">
                                    Products  <div class="badge bg-white text-success font-bold border-0 ml-3">{{store.products_count}}</div>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div v-else>
                <div class="w-full flex flex-col items-center my-6">
                    <a type="button" :href="route('store.index')" class="btn glass bt-wide btn-outline btn-default rounded-lg">
                        <i class="fa-solid fa-store pr-3 text-lg"></i>
                        Create your first store
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Checkbox from '@/Components/Checkbox.vue';
import {useToast} from "vue-toastification";
import StoreCard from "@/Components/Stores/storeCard.vue";
import {debounce} from "lodash";


const toast = useToast();

export default {
    name: "Products",
    components: {
        StoreCard,
        Checkbox,
    },
    props: {
        stores: Array,
    },
    mounted() {
        this.searchedProducts = this.products
    },
    watch: {
        search: debounce(function(value){
            this.$inertia.get('/dashboard',{ search : value},
                {
                    preserveState:true,
                    replace :true,
                })
        },300)
    },
    data() {
        return {
        };
    },
    methods: {
    }
}
</script>
