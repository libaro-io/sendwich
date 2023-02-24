<template>
    <div v-if="orders.length" class="bg-white shadow sm:rounded-lg col-span-3">
        <div class="px-4 py-5 sm:p-6">

            <h2>Assigned Orders</h2>
            <table class="mt-5 table w-full" v-if="orders.length !== 0" v-for="(orderGroup, runner) in orders">
                <thead>
                <tr>
                    <td>
                        Assigned to {{ runner }}
                    </td>
                    <td>Ordered by</td>
                    <td>Price</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="orderItem in orderGroup" class="text-sm">
                    <td class="text-sm">{{ orderItem.product.name }} <span
                        v-if="orderItem.comment">({{ orderItem.comment }})</span></td>
                    <td class="text-sm">{{orderItem.user.name }}</td>
                    <td class="text-sm">  <span class="badge badge-warning text-sm">€ {{orderItem.total }}</span></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";

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
    props: {},
    computed: {},
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
