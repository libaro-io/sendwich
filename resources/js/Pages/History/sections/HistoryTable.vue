<script>
import axios from "axios";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import moment from "moment"
import {useHelpers} from "@/Composables/helpers";

const helper = useHelpers();
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
        formatMoney: helper.formatMoney,
        getData() {
            axios.post(route('orders.by-date'), {}).then(response => {
                this.orders = response.data;
            }).catch(error => {
                console.log(error);
            });
        },
        currentDateTime(orderByDate) {
            return moment(orderByDate, "YYYYMMDD").format('DD/MM/YYYY');
        },
        formatDateTime(dateTime) {
            return moment(dateTime).format('DD/MM/YYYY HH:mm');
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
            axios.post(route('history.update-order'), {data}).then(response => {
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
            axios.patch(route('order.weight'), {
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
            axios.post(route('history.update-runner'), {
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
    <div class="panel">
        <h1 class="history__title">History</h1>
        <article v-for="group in orders" :key="group.date" class="history__group">
            <h2 class="history__date">{{ currentDateTime(group.date) }}<span v-if="getStoreNames(group)" class="history__stores"> — {{ getStoreNames(group) }}</span></h2>
            <div v-for="(orderGroup, user_id) in group.data" :key="user_id" class="history__order-group">
                <div>
                    <p v-if="orderGroup[0].delivered_at" class="history__delivered">
                        Delivered on {{ formatDateTime(orderGroup[0].delivered_at) }}
                    </p>
                    <div class="history__scroll">
                        <table class="table-brut">
                            <thead>
                            <tr>
                                <th>Ordered by</th>
                                <th>Product</th>
                                <th>Weight</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="history__num">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in orderGroup" :key="item.id">
                                <td class="history__cell-user">{{ item.user.name }}</td>
                                <td v-if="orderGroup[0].deliverer && $page.props.auth.user.id !== orderGroup[0].deliverer.id" class="history__cell-product">
                                    <span class="history__product-name">{{ item.product?.name ?? 'Removed product' }}</span>
                                    <br v-if="item.comment">
                                    <span class="history__comment">{{ item.comment }}</span>
                                </td>
                                <td v-else>
                                    <select v-if="item.product" class="field-select history__product-select" v-model="item.product.id"
                                            @change="showSaveButton(group)">
                                        <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                                    </select>
                                    <span v-else class="history__comment">Removed product</span>
                                </td>
                                <td>
                                    <template v-if="item.product?.variable_price">
                                        <template v-if="orderGroup[0].deliverer && $page.props.auth.user.id === orderGroup[0].deliverer.id">
                                            <div class="history__weight-row">
                                                <input type="number" v-model="item.weight" min="0" step="0.01"
                                                       class="field-input history__weight-input" placeholder="0.00"/>
                                                <span class="history__weight-unit">kg</span>
                                            </div>
                                            <button class="chunk chunk--teal chunk--sm history__save-btn" @click="updateWeight(item)">Save</button>
                                        </template>
                                        <span v-else class="history__weight-text">{{ item.weight ? item.weight + ' kg' : '—' }}</span>
                                    </template>
                                </td>
                                <td class="history__cell-price">
                                    <template v-if="item.product">
                                        {{ formatMoney(item.product.price) }}{{ item.product.variable_price ? '/kg' : '' }}
                                    </template>
                                    <template v-else>—</template>
                                </td>
                                <td class="history__cell-qty">{{ item.quantity }}</td>
                                <td class="history__cell-total">{{ formatMoney(item.total) }}</td>
                            </tr>
                            <tr class="history__runner-row">
                                <td colspan="4">
                                    <div class="history__runner-wrap">
                                        <span class="history__runner-label">Runner:</span>
                                        <select class="field-select history__runner-select"
                                                :value="orderGroup[0].paid_by"
                                                @change="updateRunner(orderGroup, $event.target.value)">
                                            <option :value="null">No runner</option>
                                            <option v-for="user in users" :key="user.id" :value="user.id">
                                                {{ user.id === $page.props.auth.user.id ? 'You (' + user.name + ')' : user.name }}
                                            </option>
                                        </select>
                                    </div>
                                </td>
                                <td class="history__update-cell">
                                    <button v-if="group.showSaveButton" @click="updateOrder(group)"
                                            class="chunk chunk--teal chunk--sm history__update-btn">Update
                                    </button>
                                </td>
                                <td class="history__total-cell">
                                    Total: <span class="history__total-value">{{ formatMoney(totalOrders(orderGroup)) }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </article>
    </div>
</template>

<style scoped>
@import "@css/pages/history/sections/history.css";
</style>