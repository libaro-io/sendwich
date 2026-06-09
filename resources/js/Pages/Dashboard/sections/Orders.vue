<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import {Link, router} from "@inertiajs/vue3"
import {useHelpers} from "@/Composables/helpers";
import Modal from "@/Components/ui/Modal.vue";

const helper = useHelpers();
const toast = useToast();

export default {
    name: "Orders",
    components: {
        FontAwesomeIcon, Link, Modal
    },
    data() {
        return {
            request: null,
            showDeliveryModal: false,
            deliveryOrders: [],
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
            return this.orders.some(o => o.departed_at !== null);
        },
        isRunnerNotDeparted() {
            const userId = this.$page.props.auth.user.id;
            return this.orders.some(o => o.paid_by === userId && o.departed_at === null && o.delivered_at === null);
        },
        deliveryTotal() {
            return this.deliveryOrders.reduce((sum, o) => {
                const price = o.product.variable_price && o.weight
                    ? o.product.price * o.weight
                    : o.total;
                return sum + price;
            }, 0);
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
        statusBadgeClass(order) {
            if (order.delivered_at) return 'tag--teal';
            if (order.departed_at) return 'tag--sun';
            if (order.paid_by) return 'tag--ink';
            return '';
        },
        openDeliveryModal() {
            const userId = this.$page.props.auth.user.id;
            this.deliveryOrders = this.orders
                .filter(o => o.paid_by === userId && !o.delivered_at)
                .map(o => ({ ...o, weight: o.weight ?? null }));
            this.showDeliveryModal = true;
        },
        async confirmDelivery() {
            const missingWeight = this.deliveryOrders.filter(o => o.product.variable_price && !o.weight);
            if (missingWeight.length) {
                toast.error(`Please fill in the weight for: ${missingWeight.map(o => o.product.name).join(', ')}`);
                return;
            }
            const variableOrders = this.deliveryOrders.filter(o => o.product.variable_price && o.weight);
            for (const order of variableOrders) {
                await axios.patch(route('order.weight'), {
                    order_id: order.id,
                    weight: parseFloat(order.weight),
                });
            }
            this.showDeliveryModal = false;
            router.patch(route('order.deliver'), {}, {
                only: ['orders', 'totalPrice', 'flash'],
            });
        },
    },
    props: {
        deliveryMoment: String,
        orders: Array,
        totalPrice: Number,
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
                      as="button"
                      type="button"
                      class="chunk chunk--teal chunk--sm"
                      :only="['orders','totalPrice','flash']"
                >Assign to me
                </Link>
                <Link :href="route('order.depart')"
                      v-if="isRunnerNotDeparted"
                      method="patch"
                      as="button"
                      type="button"
                      class="chunk chunk--sm chunk--sun"
                      :only="['orders','totalPrice','flash']"
                >I'm leaving
                </Link>
                <button v-if="isRunner && hasRunnerDeparted"
                        type="button"
                        class="chunk chunk--teal chunk--sm"
                        @click="openDeliveryModal"
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
                        <span class="orders__store">{{ order.store_name }}</span>{{ order.product.name }} <span v-if="order.comment" class="orders__comment">({{ order.comment }})</span>
                    </div>
                    <div class="orders__meta">
                        <span class="tag tag--teal tag--semibold">{{ formatMoney(order.total) }}</span>
                        <span class="orders__sep" aria-hidden="true">&middot;</span>
                        <span class="tag tag--sun">{{ order.user.name }}</span>
                        <span class="orders__sep" aria-hidden="true">&middot;</span>
                        <span :class="statusBadgeClass(order)" class="tag">{{ statusLabel(order) }}</span>
                    </div>
                </div>
                <div class="orders__row-actions">
                    <Link v-if="order.user_id === $page.props.auth.user.id && !order.delivered_at"
                          :href="route('order.remove-product')"
                          method="post"
                          as="button"
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

    <Modal :open="showDeliveryModal" title="Confirm delivery" @close="showDeliveryModal = false">
        <table class="table-brut orders__table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Person</th>
                    <th class="orders__num">Price</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="order in deliveryOrders" :key="order.id">
                    <td>
                        <span class="orders__cell-name">{{ order.product.name }}</span>
                        <span v-if="order.comment" class="orders__cell-comment">({{ order.comment }})</span>
                        <div v-if="order.product.variable_price" class="orders__weight-row">
                            <input type="number" v-model="order.weight" min="0" step="0.01" class="field-input orders__weight-input" placeholder="0.00"/>
                            <span class="orders__weight-unit">kg</span>
                            <span v-if="order.weight" class="orders__weight-calc">
                                = {{ formatMoney(order.product.price * order.weight) }}
                            </span>
                        </div>
                    </td>
                    <td class="orders__cell-person">{{ order.user.name }}</td>
                    <td class="orders__cell-price">
                        {{ order.product.variable_price && order.weight
                            ? formatMoney(order.product.price * order.weight)
                            : formatMoney(order.total) }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="orders__total-row">
                    <td colspan="2" class="orders__total-label">Total</td>
                    <td class="orders__total-value">{{ formatMoney(deliveryTotal) }}</td>
                </tr>
            </tfoot>
        </table>

        <template #actions>
            <button class="chunk chunk--teal" @click="confirmDelivery">Confirm</button>
            <button class="chunk chunk--ghost" @click="showDeliveryModal = false">Cancel</button>
        </template>
    </Modal>
</div>
</template>

<style scoped>
@import "@css/pages/dashboard/sections/orders.css";
</style>