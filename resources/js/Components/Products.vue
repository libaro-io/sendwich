<template>
    <div class="bg-white shadow sm:rounded-md">
        <div class="px-4 py-5 sm:p-6">
            <h2>Menu</h2>
            <div v-if="productCount">
                <input type="text" placeholder="Search a product" class="input input-bordered input-info w-full"
                       v-model="search"/>
            </div>
            <div v-if="productCount" class="mt-5 flex flex-col gap-2">
                <div v-for="(product , index) in products"
                     :key="index"
                     class="card card-compact bg-gray-50 shadow">
                    <product-card :product="product" @ordered="addProduct"></product-card>
                </div>
            </div>
            <a v-else class="alert alert-success shadow-md hover:shadow-lg cursor-pointer" :href="route('store.index')">
                <div>
                    <div>
                        <h3 class="font-bold">Nothing to feed yourself</h3>
                        <div class="">Add a store and products to get yourself started</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Checkbox from '@/Components/Checkbox.vue';
import {useToast} from "vue-toastification";
import ProductCard from "@/Components/Products/productCard.vue";
import {debounce} from "lodash";


const toast = useToast();

export default {
    name: "Products",
    components: {
        ProductCard,
        Checkbox,
    },
    props: {
        products: Array,
        filters : Object,
        productCount : Number,
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
            selectedOptions: [],
            search: this.filters.search,
            searchedProducts: null,
        };
    },
    methods: {

        addProduct(product) {
            this.$inertia.post('/api/order/add-product', {
                product_id: product.id,
                options: this.getSelectedOptionIdsForProduct(product),
            },{
                only:['orders','totalPrice','flash','selectedRunner']
            });
        },

        getSelectedOptionIdsForProduct(product) {
            return product.options.filter(option => option.selected === true).map(option => option.id);
        },
    }
}
</script>
