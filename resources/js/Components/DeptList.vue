<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">The Blacklist</h3>
            <table class="mt-5 table w-full">
                <tbody>
                    <tr v-for="user in users" class="text-sm">
                        <td class="text-sm">{{ user.name }}</td>
                        <td>
                            <span :class="user.dept > 0 ? 'badge-success' : 'badge-warning'" class="badge text-sm">€ {{user.dept }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
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
