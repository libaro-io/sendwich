<script setup>
import mitt from 'mitt'

import {Head} from '@inertiajs/inertia-vue3';
import Orders from "@/Components/Orders.vue";
import Products from "@/Components/Products.vue";
import DeptList from "@/Components/DeptList.vue";
import DoneOrders from "@/Components/DoneOrders.vue";
import {onMounted , onUnmounted} from "vue";
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
    selectedRunner: Object,
    products: Array,
    users: Array,
    deliveryMoment: String,
    company: Object,
    orders: Array,
    totalPrice: Number,
    filters:Object,
    formattedOrders : Object,
});

let intervalId;

onMounted(()=>{
    intervalId = setInterval(() => {
          Inertia.get('/dashboard',{},
            {
                preserveState:true,
                replace :true,
                only : ['orders','selectedRunner','deliveryMoment','flash']
            })
        console.log('test');
    }, 6 * 1000);
})
onUnmounted(() => clearInterval(intervalId));


</script>
<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
export default {
    export : { BreezeAuthenticatedLayout },
    layout : BreezeAuthenticatedLayout ,
}
</script>
<template>
    <Head title="Dashboard"/>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 md:col-span-1 grid-cols-3">
                        <div class="grid grid-cols-3 gap-4">
                            <Orders :orders="orders" :totalPrice="totalPrice" :delivery-moment="deliveryMoment" class="col-span-3"></Orders>
                            <done-orders :orders="formattedOrders"></done-orders>
                            <DeptList :users='users' :runner='selectedRunner' :company="company" class="col-span-3"></DeptList>
                        </div>
                    </div>
                    <Products :products="products" :filters="filters" class="col-span-2 md:col-span-1"/>
                </div>
            </div>
        </div>
</template>
