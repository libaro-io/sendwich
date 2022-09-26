<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-3xl leading-6 font-medium text-gray-900 mb-5 font-bold">History</h2>
            <article v-for="(group, orderByDate) in orders" class="mb-4">
                <h3>{{currentDateTime(orderByDate)}}</h3>
                <div v-for="(orders, user_id) in group">
                    <h4 v-if="orders[0].deliverer !== null">{{orders[0].deliverer.name}}</h4>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <!-- head -->
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- row 1 -->
                            <tr v-for="order in orders">
                                <td>{{order.user.name}}</td>
                                <td>
                                    {{ order.product.name }}
                                    <br>
                                    {{ order.comment }}
                                </td>
                                <td>{{ order.quantity }}</td>
                                <td>€ {{ order.total }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import moment from "moment"
const toast = useToast();

export default {
    name: "History",
    components: {
        FontAwesomeIcon,
    },
    mounted() {
        this.getData()
    },
    data() {
        return {
            orders: [],
        };
    },
    props: {},
    computed: {},
    methods: {
        getData() {
            axios.post('/api/getAllOrdersByDateAndUser', {}).then(response => {
                this.orders = response.data.orders;
            }).catch(error => {
                console.log(error);
            });
        },
        currentDateTime(orderByDate) {
            return moment(orderByDate,  "YYYYMMDD").format('DD/MM/YYYY')
        }
    }
}
</script>

<style scoped>

</style>
