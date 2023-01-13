<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-3xl leading-6 font-medium text-gray-900 mb-5 font-bold">History</h2>
            <article v-for="group in orders" class="mb-4">
                <h3 class="mb-2 font-bold">{{currentDateTime(group.date)}}</h3>
                <div v-for="(orderGroup, user_id) in group.data" class="">
                    <div class="ml-4">
                        <h4 v-if="orderGroup[0].deliverer !== null" class="font-medium mb-2">{{orderGroup[0].deliverer.name}}</h4>
                        <h4 v-else class="font-medium mb-2">no runner was assigned</h4>
                        <div class="overflow-x-auto mb-5">
                            <table class="table w-full">
                                <!-- head -->
                                <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th class="text-right">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- row 1 -->
                                <tr v-for="item in orderGroup">
                                    <td width="25% ">{{item.user.name}}</td>
                                    <td>
                                        <strong>
                                            {{ item.product.name }}
                                        </strong>
                                        <br>
                                        {{ item.comment }}
                                    </td>
                                    <td width="10%">{{ item.quantity }}</td>
                                    <td width="10%" class="font-bold text-right">{{ new Intl.NumberFormat('nl-BE', { style: 'currency', currency: 'EUR' }).format(item.total) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right">Total: <span class="font-bold">{{ new Intl.NumberFormat('nl-BE', { style: 'currency', currency: 'EUR' }).format(totalOrders(orderGroup)) }}</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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
    methods: {
        getData() {
            axios.post('/api/getAllOrdersByDateAndUser', {}).then(response => {
                this.orders = response.data;
            }).catch(error => {
                console.log(error);
            });
        },
        currentDateTime(orderByDate) {
            return moment(orderByDate,  "YYYYMMDD").format('DD/MM/YYYY');
        },
        totalOrders(orders){
            let sum = 0;
            orders.forEach((order) => {
                sum = sum + order.total
            })
            return sum;
        }
    }
}
</script>
