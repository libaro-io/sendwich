<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/vue3';
import History from '@/Pages/History/sections/HistoryTable.vue'

const props = defineProps({
    deliveryMoment: String,
    company: Object,
    products: Array,
    users: Array,
});
</script>

<template>
    <Head title="History"/>
    <BreezeAuthenticatedLayout>
        <div class="history-page">
            <div class="history-page__body">
                <div class="page">
                    <div class="page-container">
                        <div class="history-page__grid">
                            <History :products="products" :users="users" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<style scoped>
@import "@css/pages/history.css";
</style>
