<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Bon'app</h3>
            <div class="mt-5">
                <div v-for="product in products"
                     class="gap-4">
                    <product-card :product="product"></product-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Checkbox from '@/Components/Checkbox.vue';
import { useToast } from "vue-toastification";
import ProductCard from "@/Components/Products/productCard.vue";




const toast = useToast();

export default {
    name: "Products",
    components: {
        ProductCard,
        Checkbox,
    },
    props: {
        deliveryMoment: String,
        products: Array,
    },
    data() {
        return {
            selectedOptions: [],
        };
    },
    methods: {
        addProduct (product) {
            axios.post('/api/order/add-product', {
                product_id: product.id,
                options: this.getSelectedOptionIdsForProduct(product.id),
            }).then(response => {
                toast.success(response.data.message);
                this.emitter.emit('updateOrders');
            }).catch(error => {
                console.error(error);
            });
        },

        getSelectedOptionIdsForProduct(productId) {
            return this.selectedOptions.filter(o => o.product_id === productId).map(o => o.id);
        },

        addOrRemoveOption(shouldAdd, option) {
            if (shouldAdd) {
                this.addOption(option);
            } else {
                this.removeOption(option);
            }
        },

        addOption(option) {
            this.selectedOptions.push(option);
        },

        removeOption(option) {
            const index = this.selectedOptions.indexOf(option);

            this.selectedOptions.splice(index, 1);
        },
    }
}
</script>
