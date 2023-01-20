<template>
    <div class="bg-white shadow sm:rounded-lg col-span-3">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h2>Orders already assigned</h2>
            </div>
            <div class="mb-5">
                <div v-if="orders.length !== 0" v-for="(orderGroup, runner) in orders"
                     class="rounded-md mb-1 px-6 py-5 ">
                    <p class="mb-3">{{runner}}</p>
                    <div class="bg-gray-50" v-for="orderItem in orderGroup">
                        <div class="">
                            <div class="mt-3 sm:ml-4  px-6 py-5">
                                <div class="text-sm font-medium text-gray-900">
                                    {{orderItem.product.name}} <span v-if="orderItem.comment">({{orderItem.comment}})</span>
                                </div>
                                <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center">
                                    <div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                    >
                                        €{{orderItem.total}}
                                    </span>
                                    </div>
                                    <span class="hidden sm:mx-2 sm:inline"
                                          aria-hidden="true"> &middot; </span>
                                    <div class="mt-1 sm:mt-0"><span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{
                                            orderItem.user.name
                                        }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    No orders assigned
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import axios from "axios";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useToast } from "vue-toastification";

const toast = useToast();

export default {
    name: "DoneOrders",
    components: {
        FontAwesomeIcon,
    },
    mounted() {
        this.getOrders();
        this.emitter.on("updateOrders", this.getOrders)
    },
    data() {
        return {
            orders: [],
        };
    },
    props: {

    },
    computed: {

    },
    methods: {
        getOrders() {
            console.log('Get orders');
            const app = this;
            axios.post('/api/done-orders', {}).then(response => {
                app.orders = response.data.orders;
            }).catch(error => {
                console.log(error);
            });
        },
    }
}
</script>

<style scoped>

</style>
