<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 capitalize">Bestellingen voor {{
                    deliveryMoment
                }}</h3>
            <div class="mt-5">
                <div v-for="order in orders"
                     class="rounded-md mb-1 bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:ml-4">
                            <div class="text-sm font-medium text-gray-900">{{
                                    order.product.name
                                }}
                                 ({{
                                    order.comment}})
                            </div>
                            <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center">
                                <div><span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">€ {{
                                        order.product.price
                                    }}</span></div>
                                <span class="hidden sm:mx-2 sm:inline"
                                      aria-hidden="true"> &middot; </span>
                                <div class="mt-1 sm:mt-0"><span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{
                                        order.user.name
                                    }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 shadow sm:rounded-lg px-4 py-5 bg-yellow-50">
                    <div>
                        <h3 v-if="!user" class="text-lg leading-6 text-yellow-600 text-center" >Leverancier: None found</h3>
                        <h3 v-else class="text-lg leading-6 text-emerald-600 text-center" >{{user.name}}
                        <span><br> You are the chosen one</span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Orders",
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
            user: null
        };
    },
    props: {
        deliveryMoment: String,
    },
    methods: {
        getOrders() {
            const app = this;
            axios.post('/api/orders', {}).then(response => {
                console.log(response.data.orders);
                app.orders = response.data.orders;
                app.user = response.data.user;
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
