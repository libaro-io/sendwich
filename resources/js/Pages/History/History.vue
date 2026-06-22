<script setup lang="ts">
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/vue3';
import History from '@/Pages/History/sections/HistoryTable.vue'
import type {Product, User, Store, Company} from '@interfaces/dashboard';

defineProps<{
    deliveryMoment?: string;
    company?: Company;
    products: Product[];
    users: User[];
    stores: Store[];
}>();
</script>

<template>
    <Head title="History"/>
    <BreezeAuthenticatedLayout>
        <div class="history-page">
            <div class="history-page__body">
                <div class="page">
                    <div class="page-container">
                        <div class="history-page__grid">
                            <History :products="products" :users="users" :stores="stores" />
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
