<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import {Link, router} from "@inertiajs/vue3"
import {useHelpers} from "@/Composables/helpers";

const helper = useHelpers();
const toast = useToast();

export default {
    name: "Orders",
    components: {
        FontAwesomeIcon, Link
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
            if (order.paid_by) return 'Runner assigned';
            return 'Open';
        },
        statusBadgeClass(order) {
            if (order.delivered_at) return 'badge-success';
            if (order.departed_at) return 'badge-warning';
            if (order.paid_by) return 'badge-info';
            return 'badge-ghost';
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
                await axios.patch('/api/order/weight', {
                    order_id: order.id,
                    weight: parseFloat(order.weight),
                });
            }
            this.showDeliveryModal = false;
            router.patch('/api/order/deliver', {}, {
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
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h2>Orders for {{ deliveryMoment }}</h2>
                <div class="flex gap-2">
                    <Link href="/api/assign-to-me"
                          v-if="hasPendingOrders"
                          method="post"
                          as="button"
                          type="button"
                          class="btn btn-sm btn-primary"
                          :only="['orders','totalPrice','flash']"
                    >Assign to me
                    </Link>
                    <Link href="/api/order/depart"
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
                            @click="openDeliveryModal"
                    >All delivered
                    </button>
                </div>
            </div>
            <div class="mb-5 flex flex-col gap-2">
                <div v-for="order in orders"
                     class="card card-compact bg-gray-50 shadow-sm" v-if="orders.length">
                    <div class="sm:flex sm:items-start card-body">
                        <div class="text-sm font-medium text-gray-900 flex-1">
                            <span class="text-gray-500 inline-block mr-4">{{ order.store_name }}</span>{{ order.product.name }} <span v-if="order.comment">({{ order.comment }})</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center gap-2 flex-wrap">
                            <span class="badge badge-outline badge-success">{{ formatMoney(order.total) }}</span>
                            <span class="hidden sm:inline" aria-hidden="true">&middot;</span>
                            <span class="badge badge-info">{{ order.user.name }}</span>
                            <span class="hidden sm:inline" aria-hidden="true">&middot;</span>
                            <span :class="statusBadgeClass(order)" class="badge">{{ statusLabel(order) }}</span>
                        </div>
                        <div class="card-actions justify-end w-full">
                            <Link v-if="order.user_id === $page.props.auth.user.id && !order.delivered_at"
                                  href="/api/order/remove-product"
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

    <Teleport to="body">

        <div v-if="showDeliveryModal" class="modal modal-open modal-bottom sm:modal-middle">
            <div class="modal-box">
                <h3 class="text-lg font-bold mb-4">Confirm delivery</h3>
                <table class="table w-full mb-4">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Person</th>
                            <th class="text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in deliveryOrders" :key="order.id">
                            <td>
                                <span class="font-medium">{{ order.product.name }}</span>
                                <span v-if="order.comment" class="text-gray-500 text-sm ml-1">({{ order.comment }})</span>
                                <div v-if="order.product.variable_price" class="flex items-center gap-2 mt-1">
                                    <input type="number" v-model="order.weight" min="0" step="0.01"
                                           class="input input-bordered input-xs w-24" placeholder="0.00"/>
                                    <span class="text-xs text-gray-500">kg</span>
                                    <span v-if="order.weight" class="text-xs font-medium">
                                        = {{ formatMoney(order.product.price * order.weight) }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-sm">{{ order.user.name }}</td>
                            <td class="text-right font-medium">
                                {{ order.product.variable_price && order.weight
                                    ? formatMoney(order.product.price * order.weight)
                                    : formatMoney(order.total) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="font-bold">Total</td>
                            <td class="text-right font-bold">{{ formatMoney(deliveryTotal) }}</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="modal-action">
                    <button class="btn" @click="showDeliveryModal = false">Cancel</button>
                    <button class="btn btn-success" @click="confirmDelivery">Confirm</button>
                </div>
            </div>
        </div>
    </Teleport>
</div>
</template>

