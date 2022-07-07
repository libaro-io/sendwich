<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Users with an order for {{
                    deliveryMoment
                }}</h3>
            <div class="mt-5">
                <div v-for="user in users"
                     class="rounded-md mb-1 bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ user }}
                            </div>
                        </div>
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
    components: {},
    mounted() {
        setInterval(() => {
            this.getUsersWithOrders(this.company);
        }, 60 * 1000);
        this.getUsersWithOrders(this.company);
        this.emitter.on("updateOrders", this.getUsersWithOrders(this.company));
    },
    data() {
        return {
            users: []
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
            axios.post('/api/orders?company_token='+ company_token, {}).then(response => {
                // console.log(response.data.orders);
                app.users = response.data.orders.map(x => x.user.name).filter((v,i,s) => s.indexOf(v) === i);
                // console.log(app.users);
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
