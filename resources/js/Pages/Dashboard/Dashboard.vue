<script setup lang="ts">
import {Head, router} from '@inertiajs/vue3';
import {onMounted, onUnmounted, ref, computed} from "vue";
import Orders from "@/Pages/Dashboard/sections/Orders.vue";
import Products from "@/Pages/Dashboard/sections/Products.vue";
import Menu from "@/Pages/Dashboard/sections/Menu.vue";
import DeptList from "@/Pages/Dashboard/sections/DeptList.vue";
import type {Order, Product, Store, User, Company, DashboardFilters} from '@interfaces/dashboard';

const props = defineProps<{
    selectedRunner?: User | null;
    products: Product[];
    stores: Store[];
    users: User[];
    companyUsers: User[];
    userCount: number;
    productCount: number;
    deliveryMoment: string;
    company: Company;
    orders: Order[];
    totalPrice: number;
    filters: DashboardFilters;
}>();

let interval: ReturnType<typeof setInterval>;

const selectedStore = ref<Store | null>();

const orderingBlocked = computed(() => props.orders.some(o => o.departed_at !== null && o.delivered_at === null));

const selectStore = (store: Store): void => {
    selectedStore.value = store;
};

const unsetStore = (): void => {
    selectedStore.value = null;
};

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
});
onUnmounted(() => clearInterval(interval));
</script>
<script lang="ts">
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';

export default {
    layout: BreezeAuthenticatedLayout,
};
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
