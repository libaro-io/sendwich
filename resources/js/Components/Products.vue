<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2>Menu</h2>
            <div>
                <input type="text" placeholder="Search a product" class="input input-bordered input-info w-full"
                       v-model="search"/>
            </div>
            <div class="mt-5 flex flex-col gap-2">
                <div v-for="(product , index) in searchedProducts"
                     :key="index"
                     class="card card-compact bg-gray-50 shadow">
                    <product-card :product="product" @ordered="addProduct"></product-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
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
    },
    mounted() {
        this.searchedProducts = this.products
    },
    watch: {
        search(query, oldQuery) {
            console.log(query)
            query = query.toLowerCase();
            this.searchedProducts = this.products.filter(product => {
                const productName = product.name.toLowerCase();
                const storeName = product.name.toLowerCase();
                return (productName.includes(query) || storeName.includes(query))
            })
        }
    },
    data() {
        return {
            selectedOptions: [],
            search: '',
            searchedProducts: null
        };
    },
    methods: {

        addProduct(product) {
            this.$inertia.post('/api/order/add-product', {
                product_id: product.id,
                options: this.getSelectedOptionIdsForProduct(product),
            },{
                onSuccess: toast.success('Pisto is besteld!'),
                only:['orders','flash']
            });

        /*addProduct(product) {
            axios.post('/api/order/add-product', {
                product_id: product.id,
                options: this.getSelectedOptionIdsForProduct(product),
            }).then(response => {
                toast.success(response.data.message);
                this.emitter.emit('updateOrders');
            }).catch(error => {
                console.error(error);
            });*/
        },

        getSelectedOptionIdsForProduct(product) {
            return product.options.filter(option => option.selected === true).map(option => option.id);
        },
    }
}
</script>
