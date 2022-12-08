<script setup>
import mitt from 'mitt'

import {Head} from '@inertiajs/inertia-vue3';
import Orders from "@/Components/Orders.vue";
import SelectedRunner from "@/Components/SelectedRunner.vue";
import Products from "@/Components/Products.vue";
import DeptList from "@/Components/DeptList.vue";
import DoneOrders from "@/Components/DoneOrders.vue";

const props = defineProps({
    products: Array,
    users: Array,
    deliveryMoment: String,
    company: Object,
});

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
                            <SelectedRunner :company="company" class="col-span-3 " />
                            <Orders :delivery-moment="deliveryMoment" class="col-span-3"></Orders>
                            <done-orders></done-orders>
                            <DeptList class="col-span-3"></DeptList>
                        </div>
                    </div>
                    <Products :products="products" :deliveryMoment="deliveryMoment" class="col-span-2 md:col-span-1"/>
                </div>
            </div>
        </div>
</template>
