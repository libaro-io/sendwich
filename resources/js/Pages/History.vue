<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import History from '@/Components/History.vue'

const props = defineProps({
    deliveryMoment: String,
    company: Object
});

</script>
<template>
    <Head title="History"/>
    <BreezeAuthenticatedLayout>
    <div class="min-h-screen bg-gray-100">
        <div class="font-sans text-gray-900 antialiased">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 gap-4">
                       <History></History>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </BreezeAuthenticatedLayout>
</template>
