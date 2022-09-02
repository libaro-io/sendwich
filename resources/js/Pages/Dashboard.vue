<script setup>
import mitt from 'mitt'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import Orders from "@/Components/Orders.vue";
import SelectedRunner from "@/Components/SelectedRunner.vue";
import Products from "@/Components/Products.vue";
import DeptList from "@/Components/DeptList.vue";

const props = defineProps({
    products: Array,
    users: Array,
    deliveryMoment: String,
    company: Object,
});



</script>
<template>
    <Head title="Dashboard"/>
    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 md:col-span-1 grid-cols-3">
                        <div class="grid grid-cols-3 gap-4">
                            <SelectedRunner :company="company" class="col-span-3 " />
                            <Orders :delivery-moment="deliveryMoment" class="col-span-2"></Orders>
                            <DeptList class="col-span-1"></DeptList>
                        </div>
                    </div>
                    <Products :products="products" :deliveryMoment="deliveryMoment" class="col-span-2 md:col-span-1"/>

                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
