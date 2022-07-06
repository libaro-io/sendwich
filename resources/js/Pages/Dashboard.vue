<script setup>
import mitt from 'mitt'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import Orders from "@/Components/Orders.vue";
import Products from "@/Components/Products.vue";
import DeptList from "@/Components/DeptList.vue";

const props = defineProps({
    products: Array,
    users: Array,
    deliveryMoment: String,
});

</script>
<template>
    <Head title="Dashboard"/>
    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-4">
                    <Products :products="products" :deliveryMoment="deliveryMoment"/>
                    <div>
                        <Orders :delivery-moment="deliveryMoment"></Orders>
                        <DeptList class="mt-4"></DeptList>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
