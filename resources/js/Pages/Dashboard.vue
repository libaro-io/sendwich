<script setup>
import {Head} from '@inertiajs/inertia-vue3';
import Orders from "@/Components/Orders.vue";
import Products from "@/Components/Products.vue";
import Menu from "@/Components/Menu.vue";
import DeptList from "@/Components/DeptList.vue";
import DoneOrders from "@/Components/DoneOrders.vue";
import {onMounted, onUnmounted, ref} from "vue";
import {Inertia} from '@inertiajs/inertia'

const props = defineProps({
    selectedRunner: Object,
    products: Array,
    stores: Array,
    users: Array,
    userCount: Number,
    productCount: Number,
    deliveryMoment: String,
    company: Object,
    orders: Array,
    totalPrice: Number,
    filters: Object,
    formattedOrders: Object,
});

let interval;

let selectedStore = ref();

const selectStore = (store) => {
    console.log('store', store)
    selectedStore.value = store;
}

const unsetStore = () => {
    selectedStore.value = null;
}

onMounted(() => {
    interval = setInterval(() => {
        Inertia.get('/dashboard', {},
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['orders', 'selectedRunner', 'deliveryMoment', 'flash', 'totalPrice']
            })
    }, 60 * 1000);
})
onUnmounted(() => clearInterval(interval));


</script>
<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';

export default {
    export: {BreezeAuthenticatedLayout},
    layout: BreezeAuthenticatedLayout,
}
</script>
<template>
    <Head title="Dashboard"/>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 md:col-span-1 grid-cols-3">
                    <div class="grid grid-cols-3 gap-4">
                        <Orders :orders="orders" :totalPrice="totalPrice" :delivery-moment="deliveryMoment"
                                class="col-span-3"></Orders>
                        <done-orders :orders="formattedOrders"></done-orders>
                        <DeptList :userCount="userCount" :users='users' :runner='selectedRunner' :company="company"
                                  class="col-span-3"></DeptList>
                    </div>
                </div>
                <Menu v-if="!selectedStore" :stores="stores" class="col-span-2 md:col-span-1"
                      @select-store="selectStore"/>
                <Products v-else :productCount="selectedStore.products_count" :products="selectedStore.products"
                          :filters="filters" @unset-store="unsetStore" class="col-span-2 md:col-span-1"/>
            </div>
        </div>
    </div>
</template>
