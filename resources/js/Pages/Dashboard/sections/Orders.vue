<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {Link} from "@inertiajs/vue3"
import {useHelpers} from "../../../Composables/helpers";
import DeliveryConfirmation from "@/Pages/Dashboard/sections/DeliveryConfirmation.vue";

const helper = useHelpers();

export default {
    name: "Orders",
    components: {
        FontAwesomeIcon, Link, DeliveryConfirmation
    },
    data() {
        return {
            showConfirmation: false,
        };
    },
    computed: {
        hasPendingOrders() {
            return this.orders.some(o => o.paid_by === null);
        },
        isRunner() {
            const userId = this.$page.props.auth.user.id;
            return this.orders.some(o => o.paid_by === userId && o.delivered_at === null);
        },
        hasRunnerDeparted() {
            const userId = this.$page.props.auth.user.id;
            return this.orders.some(o => o.paid_by === userId && o.departed_at !== null && o.delivered_at === null);
        },
        isRunnerNotDeparted() {
            const userId = this.$page.props.auth.user.id;
            return this.orders.some(o => o.paid_by === userId && o.departed_at === null && o.delivered_at === null);
        },
    },
    methods: {
        formatMoney: helper.formatMoney,
        statusLabel(order) {
            if (order.delivered_at) return 'Delivered';
            if (order.departed_at) return 'On the way';
            if (order.paid_by) return `Assigned to ${order.deliverer?.name ?? 'runner'}`;
            return 'Open';
        },
        statusTagClass(order) {
            if (order.delivered_at) return 'tag--teal';
            if (order.departed_at) return 'tag--sun';
            if (order.paid_by) return 'tag--ink';
            return '';
        },
    },
    props: {
        deliveryMoment: String,
        orders: Array,
        totalPrice: Number,
        companyUsers: Array,
        stores: Array,
    },
}
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
