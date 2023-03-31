<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2>Runners</h2>
            <table class="mt-5 table w-full">
                <tbody>
                <tr v-for="(user, index) in users" class="text-sm">
                    <td class="text-sm">
                        {{ shortenName(user.name, 0,15) }}
                    </td>
                    <td>
                        <div class="tooltip tooltip-primary"
                             :data-tip="simulated ? 'Simulated runner' : 'Selected runner'"
                             v-if="runner && user.id === runner.id">
                            <svg :class="simulated ? 'text-gray-400' : 'text-green-600'"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6 cursor-pointer">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                            </svg>
                        </div>
                    </td>
                    <td>
                        <template v-if="user.id ===  $page.props.auth.user.id">
                            <label for="modal-payback"
                                   :class="user.dept > 0 ? 'badge-success text-white font-bold' : 'badge-warning font-bold'"
                                   class="badge justify-end cursor-pointer">€ {{ user.dept }}</label>
                            <PayBack :user="user" :users="users"></PayBack>
                        </template>
                        <template v-else>
                            <span
                                :class="user.dept > 0 ? 'badge-success text-white font-bold' : 'badge-warning font-bold'"
                                class="badge justify-end">€ {{ user.dept }}</span>
                        </template>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import PayBack from "@/Components/PayBack.vue";

export default {
    name: "DeptList",
    components: {PayBack},
    mounted() {
        this.request = setInterval(() => {
            this.getUsers();
        }, 60 * 1000);
        this.getUsers();
        this.getSelectedRunner(this.company);

        this.emitter.on("updateOrders", this.getUsers)
        this.emitter.on("updateSelectedRunner", this.getSelectedRunner)
    },
    unmounted() {
        clearInterval(this.request)
    },
    data() {
        return {
            users: Array,
            runner: null,
            simulated: false,
            selectedUser: null,
            request: null,
        };
    },
    props: {
        deliveryMoment: String,
        company: Object,
    },
    methods: {
        shortenName(name, start, end) {
            // find the index of the last space character in the substring
            let lastSpaceIndex = name.lastIndexOf(" ", end);

            // if the last word is not complete, adjust the end index
            if (lastSpaceIndex > end - 10) {
                end = lastSpaceIndex + 1;
            }

            // get the substring and return it
            const substring = name.substring(start, end);
            return substring;
        },
        setSelectedUser(user) {
            this.selectedUser = user;
        },
        getUsers() {
            const app = this;
            axios.post('/api/users', {}).then(response => {
                app.users = response.data.users;
            }).catch(error => {
                console.log(error);
            });
        },
        getSelectedRunner(company) {
            const app = this;
            let company_token = company.token
            axios.post('/api/selected-runner?company_token=' + company_token, {}).then(response => {
                app.runner = response.data.runner;
                if (!app.runner) {
                    app.getSimulatedRunner(this.company);
                } else {
                    app.simulated = false;
                }
            }).catch(error => {
                console.log(error);
            });
        },
        getSimulatedRunner(company) {
            const app = this;
            let company_token = company.token
            axios.post('/api/simulated-runner?company_token=' + company_token, {}).then(response => {
                app.runner = response.data.runner;
                app.simulated = true;
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
