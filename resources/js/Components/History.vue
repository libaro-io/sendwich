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
        users: Array,
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

        updateWeight(item) {
            if (!item.weight) {
                toast.error('Please enter a weight');
                return;
            }
            axios.patch('/api/order/weight', {
                order_id: item.id,
                weight: parseFloat(item.weight),
            }).then(() => {
                item.total = item.product.price * item.weight;
                toast.success('Weight updated');
            }).catch(() => {
                toast.error('Failed to update weight');
            });
        },

        getStoreNames(group) {
            const names = Object.values(group.data)
                .flat()
                .map(order => order.product?.store?.name)
                .filter(Boolean);
            return [...new Set(names)].join(', ');
        },

        updateRunner(orderGroup, runnerId) {
            const orderIds = orderGroup.map(order => order.id);
            const parsedRunnerId = runnerId ? parseInt(runnerId) : null;
            axios.post('/api/updateOrderRunner', {
                order_ids: orderIds,
                runner_id: parsedRunnerId,
            }).then(() => {
                this.getData();
                toast.success('Runner updated');
            }).catch(error => {
                toast.error('Failed to update runner');
                console.log(error);
            });
        },
    }
}
</script>
<template>
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h1>History</h1>
            <article v-for="group in orders" class="mb-4">
                <h2>{{ currentDateTime(group.date) }}<span v-if="getStoreNames(group)"> — {{ getStoreNames(group) }}</span></h2>
                <div v-for="(orderGroup, user_id) in group.data" class="">
                    <div>
                        <div class="overflow-x-auto mb-5 rounded-lg shadow-sm border border-gray-100">
                            <table class="table w-full">
                                <thead>
                                <tr class="bg-white">
                                    <th>Ordered by</th>
                                    <th>Product</th>
                                    <th>Weight</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-right">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in orderGroup" class="bg-white hover:bg-gray-50">
                                    <td width="25%">{{ item.user.name }}</td>
                                    <td v-if="orderGroup[0].deliverer && $page.props.auth.user.id !== orderGroup[0].deliverer.id">
                                        <span class="font-bold text-base">{{ item.product.name }}</span>
                                        <br>
                                        <span class="text-gray-500">{{ item.comment }}</span>
                                    </td>
                                    <td v-else>
                                        <select class="w-full max-w-xs bg-transparent border-none outline-none font-bold cursor-pointer" v-model="item.product.id"
                                                @change="showSaveButton(group)">
                                            <option v-for="product in products" :value="product.id">{{ product.name }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <template v-if="item.product.variable_price">
                                            <template v-if="orderGroup[0].deliverer && $page.props.auth.user.id === orderGroup[0].deliverer.id">
                                                <div class="flex items-center gap-1">
                                                    <input type="number" v-model="item.weight" min="0" step="0.01"
                                                           class="input input-bordered input-xs w-20" placeholder="0.00"/>
                                                    <span class="text-xs text-gray-500">kg</span>
                                                </div>
                                                <button class="btn btn-xs btn-primary mt-1" @click="updateWeight(item)">Save</button>
                                            </template>
                                            <span v-else class="text-sm">{{ item.weight ? item.weight + ' kg' : '—' }}</span>
                                        </template>
                                    </td>
                                    <td>
                                        <template v-if="item.product.variable_price">
                                            {{ new Intl.NumberFormat('nl-BE', { style: 'currency', currency: 'EUR' }).format(item.product.price) }}/kg
                                        </template>
                                        <template v-else>
                                            {{ new Intl.NumberFormat('nl-BE', { style: 'currency', currency: 'EUR' }).format(item.product.price) }}
                                        </template>
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
                                <tr class="bg-white border-t border-gray-100">
                                    <td colspan="4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-600">Runner:</span>
                                        <select class="select select-sm bg-white border border-gray-200"
                                                :value="orderGroup[0].paid_by"
                                                @change="updateRunner(orderGroup, $event.target.value)">
                                            <option :value="null">No runner</option>
                                            <option v-for="user in users" :key="user.id" :value="user.id">
                                                {{ user.id === $page.props.auth.user.id ? 'You (' + user.name + ')' : user.name }}
                                            </option>
                                        </select>
                                        </div>
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
                                        }}</span>
                                    </td>
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
