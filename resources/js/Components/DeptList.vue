<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">The Blacklist</h3>
            <div class="mt-5">
                <div v-for="user in users" :class="user.dept > 0 ? 'bg-green-50': 'bg-red-50'" class="rounded-md mb-1 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                        </div>
                        <div class="mt-4 sm:mt-0 sm:ml-6 sm:flex-shrink-0">
                                <span :class="user.dept > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium">€ {{user.dept }}</span>
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
    name: "DeptList",
    mounted() {
        setInterval(() => {
            this.getUsers();
        }, 60 * 1000);
        this.getUsers();
        this.emitter.on("updateOrders", this.getUsers)
    },
    data() {
        return {
            users: Array,
        };
    },
    props: {
        deliveryMoment: String,
    },
    methods: {
        getUsers() {
            const app = this;
            axios.post('/api/users', {}).then(response => {
                app.users = response.data.users;
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
