<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h1>History</h1>
            <article v-for="group in orders" class="mb-4">
                <h2>{{ currentDateTime(group.date) }}</h2>
                <div v-for="(orderGroup, user_id) in group.data" class="">
                    <div>
                        <div class="overflow-x-auto mb-5">
                            <table class="table w-full">
                                <thead>
                                <tr>
                                    <th>Ordered by</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th class="text-right">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- row 1 -->
                                <tr v-for="item in orderGroup">
                                    <td width="25% ">{{ item.user.name }}</td>
                                    <td v-if="orderGroup[0].deliverer && $page.props.auth.user.id !== orderGroup[0].deliverer.id">
                                        <strong>
                                            {{ item.product.name }}
                                        </strong>
                                        <br>
                                        {{ item.comment }}
                                    </td>
                                    <td v-else>
                                        <select class="select w-full max-w-xs bg-white" v-model="item.product.id"
                                                @change="showSaveButton(group)">
                                            <option v-for="product in products" :value="product.id">{{ product.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td width="10%">{{ item.quantity }}</td>
                                    <td width="10%" class="font-bold text-right">{{
                                            new Intl.NumberFormat('nl-BE', {
                                                style: 'currency',
                                                currency: 'EUR'
                                            }).format(item.total)
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <span class="badge badge-success" v-if="orderGroup[0].deliverer !== null && orderGroup[0].deliverer.id === $page.props.auth.user.id  ">Thank you for this delivery</span>
                                        <span class="badge" v-else-if="orderGroup[0].deliverer !== null">Delivered by {{ orderGroup[0].deliverer.name }}</span>
                                        <span class="badge" v-else>No runner was assigned</span>
                                    </td>
                                    <td class="text-right">
                                        <button v-if="group.showSaveButton" @click="updateOrder(group)"
                                                class="btn btn-primary ml-2">Update
                                        </button>
                                    </td>
                                    <td class="text-right">
                                        Total: <span class="font-bold">{{
                                            new Intl.NumberFormat('nl-BE', {
                                                style: 'currency',
                                                currency: 'EUR'
                                            }).format(totalOrders(orderGroup))
                                        }}</span></td>
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
    props: {
        products: Array,
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
            return moment(orderByDate, "YYYYMMDD").format('DD/MM/YYYY');
        },
        totalOrders(orders) {
            let sum = 0;
            orders.forEach((order) => {
                sum = sum + order.total
            })
            return sum;
        },
        showSaveButton(group) {
            group.showSaveButton = true;
        },
        updateOrder(group) {
            const data = group.data
            axios.post('/api/updateOldOrder', {data}).then(response => {
                this.getData()
                group.showSaveButton = false;
            }).catch(error => {
                console.log(error);
            });
        },

    }
}
</script>
