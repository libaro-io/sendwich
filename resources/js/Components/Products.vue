<template>
    <div class="bg-white shadow sm:rounded-md">
        <div class="px-4 py-5 sm:p-6">
            <h2>Menu</h2>
            <div v-if="productCount">
               <div class="flex gap-2">
                   <button @click="$emit('unsetStore')" class="btn btn-round btn-info"> <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                   </svg>
                   </button>
                   <input type="text" placeholder="Search a product" class="input input-bordered input-info w-full"
                          v-model="search"/>
               </div>
            </div>
            <div v-if="filteredProducts.length" class="mt-5 flex flex-col gap-2">
                <div v-for="(product, index) in filteredProducts"
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
import Checkbox from '@/Components/Checkbox.vue';
import {useToast} from "vue-toastification";
import ProductCard from "@/Components/Products/productCard.vue";


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
    computed: {
        filteredProducts() {
            if (!this.search) {
                return this.products;
            }
            const searchTerm = this.search.toLowerCase();
            return this.products.filter(product =>
                product.name.toLowerCase().includes(searchTerm) ||
                (product.description && product.description.toLowerCase().includes(searchTerm))
            );
        }
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
            var app = this;
            var options = app.getSelectedOptionIdsForProduct(product)
            axios.post('/api/order/check-new-store', {
                product_id: product.id,
                options: options,
            }).then(function(response){
                app.confirmOrder(product , options);
            }).catch(function (error){
                app.showConfirmationModal(product);
            });
            console.log('send')
        },

        confirmOrder(product, options) {
            console.log('confirmed');
            console.log(options)
            this.$inertia.post('/api/order/add-product', {
                product_id: product.id,
                options: options,
            },{
                only:['orders','totalPrice','flash','selectedRunner']
            });
        },

        showConfirmationModal(product){
            //todo add conformation modal
            alert('you added a product from a differnt store')
        },

        getSelectedOptionIdsForProduct(product) {
            return product.options.filter(option => option.selected === true).map(option => option.id);
        },
    }
}
</script>
