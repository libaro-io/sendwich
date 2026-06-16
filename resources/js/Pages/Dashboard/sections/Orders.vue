<script setup lang="ts">
import {ref, computed} from "vue";
import {Link, usePage} from "@inertiajs/vue3";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useHelpers} from "@/Composables/helpers";
import DeliveryConfirmation from "@/Pages/Dashboard/sections/DeliveryConfirmation.vue";
import type {Order, User, Store} from '@interfaces/dashboard';

const props = defineProps<{
    deliveryMoment: string;
    orders: Order[];
    totalPrice: number;
    companyUsers: User[];
    stores: Store[];
}>();

const page = usePage();
const {formatMoney} = useHelpers();

const showConfirmation = ref(false);

const hasPendingOrders = computed(() => props.orders.some(o => o.paid_by === null));

const isRunner = computed(() => {
    const userId = page.props.auth.user?.id;
    return props.orders.some(o => o.paid_by === userId && o.delivered_at === null);
});

const hasRunnerDeparted = computed(() => {
    const userId = page.props.auth.user?.id;
    return props.orders.some(o => o.paid_by === userId && o.departed_at !== null && o.delivered_at === null);
});

const isRunnerNotDeparted = computed(() => {
    const userId = page.props.auth.user?.id;
    return props.orders.some(o => o.paid_by === userId && o.departed_at === null && o.delivered_at === null);
});

const statusLabel = (order: Order): string => {
    if (order.delivered_at) return 'Delivered';
    if (order.departed_at) return 'On the way';
    if (order.paid_by) return `Assigned to ${order.deliverer?.name ?? 'runner'}`;
    return 'Open';
};

const statusTagClass = (order: Order): string => {
    if (order.delivered_at) return 'tag--teal';
    if (order.departed_at) return 'tag--sun';
    if (order.paid_by) return 'tag--ink';
    return '';
};
</script>
<template>
<div>
    <div class="panel">
        <div class="orders__header">
            <h2 class="orders__title">Orders for {{ deliveryMoment }}</h2>
            <div class="orders__actions">
                <Link :href="route('order.assign-to-me')"
                      v-if="hasPendingOrders"
                      method="post"
                      :as="'button'"
                      type="button"
                      class="chunk chunk--teal chunk--sm"
                      :only="['orders','totalPrice','flash']"
                >Assign to me
                </Link>
                <Link :href="route('order.depart')"
                      v-if="isRunnerNotDeparted"
                      method="patch"
                      :as="'button'"
                      type="button"
                      class="chunk chunk--sm chunk--sun"
                      :only="['orders','totalPrice','flash']"
                >I'm leaving
                </Link>
                <button v-if="isRunner && hasRunnerDeparted"
                        type="button"
                        class="chunk chunk--teal chunk--sm"
                        @click="showConfirmation = true"
                >All delivered
                </button>
            </div>
        </div>
        <div class="orders__list">
            <div v-for="order in orders"
                 :key="order.id"
                 class="panel panel--flat orders__row" v-if="orders.length">
                <div class="orders__row-main">
                    <div class="orders__product">
                        <span class="orders__store">{{ order.store_name }}</span>{{ order.product ? order.product.name : order.label }} <span v-if="order.comment" class="orders__comment">({{ order.comment }})</span>
                    </div>
                    <div class="orders__meta">
                        <span class="tag tag--teal tag--semibold">{{ formatMoney(order.total) }}</span>
                        <span class="orders__sep" aria-hidden="true">&middot;</span>
                        <span class="tag tag--sun">{{ order.user.name }}</span>
                        <span class="orders__sep" aria-hidden="true">&middot;</span>
                        <span :class="statusTagClass(order)" class="tag">{{ statusLabel(order) }}</span>
                    </div>
                </div>
                <div class="orders__row-actions">
                    <Link v-if="order.user_id === $page.props.auth.user.id && !order.delivered_at"
                          :href="route('order.remove-product')"
                          method="post"
                          :as="'button'"
                          type="button"
                          class="orders__delete"
                          :data="{ product_id: order.product_id, }"
                          :only="['orders','flash','selectedRunner','totalPrice']"
                    >
                        <FontAwesomeIcon icon="fa-solid fa-trash"/>
                    </Link>
                </div>
            </div>
            <div v-else class="panel panel--flat">
                <p class="orders__empty">There are no orders yet</p>
            </div>
        </div>

        <h3 class="orders__total" v-if="totalPrice !== 0"> Total:
            {{ formatMoney(totalPrice.toFixed(2)) }}
        </h3>
    </div>

    <DeliveryConfirmation v-if="showConfirmation"
                          :orders="orders"
                          :company-users="companyUsers"
                          :stores="stores"
                          @close="showConfirmation = false"/>
</div>
</template>

<style scoped>
@import "@css/pages/dashboard/sections/orders.css";
</style>
