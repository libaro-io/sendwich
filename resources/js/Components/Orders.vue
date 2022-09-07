<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg leading-6 font-medium text-gray-900 capitalize ">Bestellingen voor {{ deliveryMoment }}</h3>
                <button v-if="orders.length !== 0"
                        @click="assignToMe()"
                        type="button"
                        class="inline-flex items-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Assign to me
                </button>
            </div>

            <div class="mb-5">
                <div v-for="order in orders"
                     class="rounded-md mb-1 bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{order.product.name}} <span v-if="order.comment">({{order.comment}})</span>
                            </div>
                            <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center">
                                <div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                    >
                                        €{{order.total}}
                                    </span>
                                </div>
                                <span class="hidden sm:mx-2 sm:inline"
                                      aria-hidden="true"> &middot; </span>
                                <div class="mt-1 sm:mt-0"><span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{
                                        order.user.name
                                    }}</span></div>
                            </div>
                        </div>
                    </div>

                    <button
                        v-if="isMyOrder(order)"
                        @click="removeProduct(order.product)"
                        class="text-red-400 hover:text-red-600"
                    >
                        <FontAwesomeIcon icon="fas-fa fa-trash" />
                    </button>
                </div>
            </div>

            <h3
                v-if="totalPrice !== 0"
            >
                €{{ totalPrice }}
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
            axios.post('/api/assign-to-me')
                .then(response => {
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
