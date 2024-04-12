<template>
    <div v-if="props.orders.length" class="bg-white shadow sm:rounded-lg col-span-3">
        <div class="px-4 py-5 sm:p-6">

            <h2>Assigned Orders</h2>
            <table class="mt-5 table w-full" v-if="props.orders.length !== 0" v-for="(orderGroup, runner) in props.orders">
                <thead>
                <tr>
                    <td>
                        Assigned to {{ runner }}
                    </td>
                    <td>Ordered by</td>
                    <td>Price</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="orderItem in orderGroup" class="text-sm">
                    <td class="text-sm">{{ orderItem.product.name }} <span
                        v-if="orderItem.comment">({{ orderItem.comment }})</span></td>
                    <td class="text-sm">{{orderItem.user.name }}</td>
                    <td class="text-sm">  <span class="badge badge-warning text-sm">{{ helper.formatMoney(orderItem.total) }}</span></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import {useToast} from "vue-toastification";
import { useHelpers} from "@/Composables/helpers";

const helper = useHelpers();
const toast = useToast();

const props = defineProps(['orders']);
</script>
