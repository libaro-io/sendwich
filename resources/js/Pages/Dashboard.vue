<script setup>
import {Head} from '@inertiajs/vue3';
import Orders from "@/Components/Orders.vue";
import Products from "@/Components/Products.vue";
import Menu from "@/Components/Menu.vue";
import DeptList from "@/Components/DeptList.vue";
import {onMounted, onUnmounted, ref, computed} from "vue";
import {router} from '@inertiajs/vue3'

const props = defineProps({
    selectedRunner: Object,
    products: Array,
    stores: Array,
    users: Array,
    companyUsers: Array,
    userCount: Number,
    productCount: Number,
    deliveryMoment: String,
    company: Object,
    orders: Array,
    totalPrice: Number,
    filters: Object,
});

let interval;

let selectedStore = ref();

const orderingBlocked = computed(() => props.orders.some(o => o.departed_at !== null && o.delivered_at === null));

const selectStore = (store) => {
    selectedStore.value = store;
}

const unsetStore = () => {
    selectedStore.value = null;
}

onMounted(() => {
    interval = setInterval(() => {
        router.get('/dashboard', {},
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
                                :company-users="companyUsers" :stores="stores"
                                class="col-span-3"></Orders>
                        <DeptList :userCount="userCount" :users='users' :runner='selectedRunner' :company="company"
                                  class="col-span-3"></DeptList>
                    </div>
                </div>
                <Menu v-if="!selectedStore" :stores="stores" class="col-span-2 md:col-span-1"
                      @select-store="selectStore"/>
                <Products v-else :productCount="selectedStore.products_count" :products="selectedStore.products"
                          :filters="filters" :ordering-blocked="orderingBlocked" @unset-store="unsetStore" class="col-span-2 md:col-span-1"/>
            </div>
        </div>
    </div>
</template>
