<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {Link} from "@inertiajs/vue3"
import {useHelpers} from "@/Composables/helpers";
import DeliveryConfirmation from "@/Components/DeliveryConfirmation.vue";

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
            if (order.paid_by) return 'Runner assigned';
            return 'Open';
        },
        statusBadgeClass(order) {
            if (order.delivered_at) return 'badge-success';
            if (order.departed_at) return 'badge-warning';
            if (order.paid_by) return 'badge-info';
            return 'badge-ghost';
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
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h2>Orders for {{ deliveryMoment }}</h2>
                <div class="flex gap-2">
                    <Link :href="route('order.assign-to-me')"
                          v-if="hasPendingOrders"
                          method="post"
                          as="button"
                          type="button"
                          class="btn btn-sm btn-primary"
                          :only="['orders','totalPrice','flash']"
                    >Assign to me
                    </Link>
                    <Link :href="route('order.depart')"
                          v-if="isRunnerNotDeparted"
                          method="patch"
                          as="button"
                          type="button"
                          class="btn btn-sm btn-warning"
                          :only="['orders','totalPrice','flash']"
                    >I'm leaving
                    </Link>
                    <button v-if="isRunner && hasRunnerDeparted"
                            type="button"
                            class="btn btn-sm btn-success"
                            @click="showConfirmation = true"
                    >All delivered
                    </button>
                </div>
            </div>
            <div class="mb-5 flex flex-col gap-2">
                <div v-for="order in orders"
                     class="card card-compact bg-gray-50 shadow-sm" v-if="orders.length">
                    <div class="sm:flex sm:items-start card-body">
                        <div class="text-sm font-medium text-gray-900 flex-1">
                            <span class="text-gray-500 inline-block mr-4">{{ order.store_name }}</span>{{ order.product ? order.product.name : order.label }} <span v-if="order.comment">({{ order.comment }})</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-600 flex items-center gap-2 flex-wrap">
                            <span class="badge badge-outline badge-success">{{ formatMoney(order.total) }}</span>
                            <span aria-hidden="true">&middot;</span>
                            <span class="badge badge-info">{{ order.user.name }}</span>
                            <span aria-hidden="true">&middot;</span>
                            <span :class="statusBadgeClass(order)" class="badge">{{ statusLabel(order) }}</span>
                        </div>
                        <div class="card-actions justify-end w-full">
                            <Link v-if="order.user_id === $page.props.auth.user.id && !order.delivered_at"
                                  :href="route('order.remove-product')"
                                  method="post"
                                  as="button"
                                  type="button"
                                  class="text-error hover:text-red-600"
                                  :data="{ product_id: order.product_id, }"
                                  :only="['orders','flash','selectedRunner','totalPrice']"
                            >
                                <FontAwesomeIcon icon="fas-fa fa-trash"/>
                            </Link>
                        </div>
                    </div>
                </div>
                <div v-else class="card card-compact bg-gray-50 shadow-sm">
                    <div class="card-body">
                        <p class="font-bold">There are no orders yet</p>
                    </div>
                </div>
            </div>

            <h3 class="pl-4"
                v-if="totalPrice !== 0"
            > Total:
                {{ formatMoney(totalPrice.toFixed(2)) }}
            </h3>
        </div>

    </div>

    <DeliveryConfirmation v-if="showConfirmation"
                          :orders="orders"
                          :company-users="companyUsers"
                          :stores="stores"
                          @close="showConfirmation = false"/>
</div>
</template>
