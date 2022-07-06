<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Bon'app</h3>
            <div class="mt-5">
                <div v-for="product in products"
                     class="rounded-md mb-1 bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                            <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center">
                                <div><span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">€ {{
                                        product.price
                                    }}</span></div>
                                <span class="hidden sm:mx-2 sm:inline"
                                      aria-hidden="true"> &middot; </span>
                                <div class="mt-1 sm:mt-0">{{ deliveryMoment }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6 sm:flex-shrink-0">
                        <button v-for="type in ['wit', 'bruin']" type="button"
                                @click="order(type, product)"
                                :class="product.selected === type  ? 'bg-green-300 text-white' : 'bg-white'"
                                class="inline-flex items-center px-4 py-2 shadow-sm font-medium rounded-md text-gray-700 hover:bg-green-100 sm:text-sm mr-1">
                            {{ type }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {useToast} from "vue-toastification";
import "vue-toastification/dist/index.css";

export default {
    setup() {
        const toast = useToast();
        return {toast}
    },
    name: "Products",
    mounted() {
    },
    data() {
        return {};
    },
    props: {
        deliveryMoment: String,
        products: Array,
    },
    methods: {
        order(type, product) {
            const app = this;
            if (product.selected === type) {
                type = '';
            }
            product.selected = type;

            axios.post('/api/order', {
                product_id: product.id,
                type: type,
            }).then(response => {
                console.log(response.data);
                app.emitter.emit('updateOrders');
                app.toast.success(response.data.message);
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
