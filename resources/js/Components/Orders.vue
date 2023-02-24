<template>
    <div v-if="orders.length" class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h2>Orders for {{ deliveryMoment }}</h2>
                <button v-if="orders.length !== 0"
                        @click="assignToMe()"
                        type="button"
                        class="btn btn-sm btn-primary">
                    Assign to me
                </button>
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
                            <button
                                v-if="isMyOrder(order)"
                                @click="removeProduct(order.product)"
                                class="text-error hover:text-red-600"
                            >
                                <FontAwesomeIcon icon="fas-fa fa-trash" />
                            </button>
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
import axios from "axios";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useToast } from "vue-toastification";

const toast = useToast();

export default {
    name: "Orders",
    components: {
        FontAwesomeIcon,
    },
    mounted() {
        setInterval(() => {
            this.getOrders();
        }, 60 * 1000);
        this.getOrders();
        this.emitter.on("updateOrders", this.getOrders)
    },
    data() {
        return {
            orders: [],
        };
    },
    props: {
        deliveryMoment: String,
    },
    computed: {
        totalPrice() {
            if (this.orders.length === 0) {
                return 0;
            }

            return this.orders.map(c => c.total).reduce((carry, total) => {
                return carry + total;
            });
        },
    },
    methods: {
        getOrders() {
            const app = this;
            axios.post('/api/orders', {}).then(response => {
                app.orders = response.data.orders;
            }).catch(error => {
                console.log(error);
            });
        },

        isMyOrder(order) {
            return order.user_id === this.$page.props.user.id;
        },

        removeProduct (product) {
            axios.post('/api/order/remove-product', {
                product_id: product.id,
            }).then(response => {
                toast.success(response.data.message);
                this.emitter.emit('updateOrders');
            }).catch(error => {
                console.error(error);
            });
        },

        assignToMe(){
            axios.post('/api/assign-to-me').then(response => {
                toast.success(response.data.message);
                this.emitter.emit('updateOrders');
            }).catch(error => {
                console.error(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
