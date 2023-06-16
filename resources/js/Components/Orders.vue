<template>
    <div v-if="orders.length" class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h2>Orders for {{ deliveryMoment }}</h2>
                <Link href="/api/assign-to-me"
                      v-if="orders.length !== 0"
                      method="post"
                      as="button"
                      type="button"
                      class="btn btn-sm btn-primary"
                      :only="['orders','totalPrice','flash']"
                >Assign to me</Link>

            </div>
            <div class="mb-5 flex flex-col gap-2">
                <div v-for="order in orders"
                     class="card card-compact bg-gray-50 shadow">
                    <div class="sm:flex sm:items-start card-body">
                        <div class="text-sm font-medium text-gray-900">
                            {{order.product.name}} <span v-if="order.comment">({{order.comment}})</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center">
                            <div>
                                <span
                                    class="badge badge-outline badge-success"
                                >
                                    €{{order.total}}
                                </span>
                            </div>
                            <span class="hidden sm:mx-2 sm:inline"
                                  aria-hidden="true"> &middot; </span>
                            <div class="mt-1 sm:mt-0"><span
                                class="badge badge-info">{{
                                    order.user.name
                                }}</span></div>
                        </div>
                        <div class="card-actions justify-end w-full">
                            <Link href="/api/order/remove-product"
                                  method="post"
                                  as="button"
                                  type="button"
                                  class="text-error hover:text-red-600"
                                  :data="{ product_id: order.product_id, }"
                                  :only="['orders','flash','selectedRunner','totalPrice']"
                            >
                                <FontAwesomeIcon icon="fas-fa fa-trash" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="pl-4"
                v-if="totalPrice !== 0"
            > Totaal :
                €{{ totalPrice.toFixed(2) }}
            </h3>
        </div>

    </div>
</template>

<script>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useToast } from "vue-toastification";
import { Link } from "@inertiajs/inertia-vue3"

const toast = useToast();

export default {
    name: "Orders",
    components: {
        FontAwesomeIcon,Link
    },
    data() {
        return {
            request: null,
        };
    },

    props: {
        deliveryMoment: String,
        orders: Array,
        totalPrice: Number,
    },
}
</script>

