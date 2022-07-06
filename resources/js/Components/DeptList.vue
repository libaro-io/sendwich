<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Wall of shame</h3>
            <div class="mt-5">
                <div v-for="user in users"
                     class="rounded-md mb-1 bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                        </div>
                        <div class="mt-4 sm:mt-0 sm:ml-6 sm:flex-shrink-0">
                                <span :class="dept ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium">€ {{user.dept }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {useToast} from "vue-toastification";
import "vue-toastification/dist/index.css";

export default {
    setup() {
        const toast = useToast();
        return {toast}
    },
    name: "DeptList",
    mounted() {
        this.getUsers();
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
                console.log(response.data.users);
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
