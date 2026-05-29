<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import {Link} from "@inertiajs/vue3"
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
        };
    },
    methods: {
        formatMoney: helper.formatMoney
    },
    props: {
        deliveryMoment: String,
        orders: Array,
        totalPrice: Number,
    },
}
</script>
<template>
    <div  class="bg-white shadow-sm sm:rounded-lg">
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
                    <Link href="/api/order/deliver"
                          v-if="isRunner"
                          method="patch"
                          as="button"
                          type="button"
                          class="btn btn-sm btn-success"
                          :only="['orders','totalPrice','flash']"
                    >Alles afgeleverd
                    </Link>
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
                            <Link v-if="order.user_id === $page.props.auth.user.id"
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
            > Totaal :
                {{ formatMoney(totalPrice.toFixed(2)) }}
            </h3>
        </div>

    </div>
</template>

