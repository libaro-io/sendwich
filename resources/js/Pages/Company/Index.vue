<script setup>
import mitt from 'mitt'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import Stores from "@/Components/Stores.vue";

const props = defineProps({
    stores: Object,
    company: Object,
});

</script>
<template>
    <Head title="Settings"/>
    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-4">
                    <Stores :stores="stores"/>
                    <div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
