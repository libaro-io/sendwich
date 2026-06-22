<script setup lang="ts">
import {ref, onMounted, onUnmounted} from "vue";
import axios from "axios";
import {emitter} from "@/Composables/emitter";
import type {DisplayCompany} from '@interfaces/display';

const props = defineProps<{
    deliveryMoment?: string;
    company: DisplayCompany;
}>();

const users = ref<string[]>([]);
let request: ReturnType<typeof setInterval>;

const getUsersWithOrders = (): void => {
    const company_token = props.company.token;
    axios.post(route('orders.index', {company_token}), {}).then(response => {
        users.value = response.data.orders
            .map((order: { user: { name: string } }) => order.user.name)
            .filter((name: string, index: number, names: string[]) => names.indexOf(name) === index);
    }).catch(error => {
        console.log(error);
    });
};

onMounted(() => {
    request = setInterval(() => {
        getUsersWithOrders();
    }, 60 * 1000);
    getUsersWithOrders();
    emitter.on("updateOrders", getUsersWithOrders);
});

onUnmounted(() => clearInterval(request));
</script>
<template>
    <div class="panel">
        <h2 class="users-orders__title">Users with an order for {{ deliveryMoment }}</h2>
        <div class="users-orders__list">
            <div v-for="user in users" :key="user" class="panel panel--flat users-orders__row">
                <div class="users-orders__name">
                    {{ user }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "@css/pages/display/sections/users-with-orders.css";
</style>
