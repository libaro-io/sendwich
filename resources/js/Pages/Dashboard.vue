<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import axios from "axios";
import { useToast } from "vue-toastification";
import "vue-toastification/dist/index.css";

const props = defineProps({
    products: Array,
    users: Array,
    orders: Array,
    deliveryMoment: String,
});

const toast = useToast();

function order(type, product) {
    if (product.selected === type) {
        type = '';
    }
    product.selected = type;

    axios.post('/api/order', {
        product_id: product.id,
        type: type,
    }).then(response => {
        console.log(response.data.message);
        toast.success(response.data.message);
    }).catch(error => {
        console.log(error);
    });
}

</script>
<template>
    <Head title="Dashboard"/>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bestel een bitje pisto's.
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-4">
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
                                                <div>€ {{ product.price }}</div>
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
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Bestellingen {{deliveryMoment}}</h3>
                            <div class="mt-5">
                                <div v-for="order in orders"
                                     class="rounded-md mb-1 bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 sm:mt-0 sm:ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ order.product.name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </BreezeAuthenticatedLayout>
</template>
