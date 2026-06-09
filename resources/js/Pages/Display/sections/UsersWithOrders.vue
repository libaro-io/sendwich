<script>
import axios from "axios";


export default {
    name: "Orders",
    components: {},
    mounted() {
        this.request = setInterval(() => {
            this.getUsersWithOrders(this.company);
        }, 60 * 1000);
        this.getUsersWithOrders(this.company);
        this.emitter.on("updateOrders", this.getUsersWithOrders(this.company));
    },
    unmounted() {
        clearInterval(this.request)
    },
    data() {
        return {
            users: [],
            request: null
        };
    },
    props: {
        deliveryMoment: String,
        company: Object,
    },
    methods: {
        getUsersWithOrders(company) {
            const app = this;
            let company_token = company.token
            axios.post('/api/orders?company_token=' + company_token, {}).then(response => {
                app.users = response.data.orders.map(x => x.user.name).filter((v, i, s) => s.indexOf(v) === i);
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>
<template>
    <div class="panel">
        <h2 class="users-orders__title">Users with an order for {{ deliveryMoment }}</h2>
        <div class="users-orders__list">
            <div v-for="user in users" :key="user" class="panel panel--flat users-orders__row">
                <div class="users-orders__name">
                    {{ user }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "@css/pages/display/sections/users-with-orders.css";
</style>
