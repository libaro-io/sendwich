<script setup>
import {Head} from '@inertiajs/vue3';
import Orders from "@/Pages/Dashboard/sections/Orders.vue";
import Products from "@/Pages/Dashboard/sections/Products.vue";
import Menu from "@/Pages/Dashboard/sections/Menu.vue";
import DeptList from "@/Pages/Dashboard/sections/DeptList.vue";
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
import BreezeAuthenticatedLayout from '@layouts/Authenticated.vue';

export default {
    export: {BreezeAuthenticatedLayout},
    layout: BreezeAuthenticatedLayout,
}
</script>
<template>
    <Head title="Dashboard"/>
    <div class="page">
        <div class="page-container">
            <div class="dashboard__grid">
                <div class="dashboard__left">
                    <div class="dashboard__stack">
                        <Orders :orders="orders" :totalPrice="totalPrice" :delivery-moment="deliveryMoment"
                                :company-users="companyUsers" :stores="stores"
                                class="dashboard__full"></Orders>
                        <DeptList :userCount="userCount" :users='users' :runner='selectedRunner' :company="company"
                                  class="dashboard__full"></DeptList>
                    </div>
                </div>
                <Menu v-if="!selectedStore" :stores="stores" class="dashboard__side"
                      @select-store="selectStore"/>
                <Products v-else :productCount="selectedStore.products_count" :products="selectedStore.products"
                          :filters="filters" :ordering-blocked="orderingBlocked" @unset-store="unsetStore" class="dashboard__side"/>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "@css/pages/dashboard.css";
</style>
