<script setup>
import mitt from 'mitt'
import UsersWithOrders from "@/Components/UsersWithOrders.vue";
import SelectedRunner from "@/Components/SelectedRunner.vue";
import {Link} from '@inertiajs/inertia-vue3';

const props = defineProps({
    deliveryMoment: String,
});

</script>
<template>
<div class="min-h-screen bg-gray-100">
    <div class="font-sans text-gray-900 antialiased">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <UsersWithOrders :delivery-moment="deliveryMoment"></UsersWithOrders>
                    </div>
                    <div>
                        <SelectedRunner class="fill-current text-gray-500" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
