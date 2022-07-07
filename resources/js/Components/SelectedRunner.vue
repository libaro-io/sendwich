<template>
    <div class="shadow sm:rounded-lg px-4 py-5 bg-cyan-100" v-if="user">
        <div>
            <h3 class="text-xl leading-6 text-cyan-600 text-center">{{ user.name }}</h3>
            <h3 class="text-md leading-6 text-cyan-600 text-center mt-2">You are the chosen one</h3>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Selected runner",
    components: {},
    mounted() {
        setInterval(() => {
            this.getSelectedRunner(this.company);
        }, 60 * 1000);
        this.getSelectedRunner(this.company);
        this.emitter.on("updateSelectedRunner", this.getSelectedRunner(this.company))
    },
    data() {
        return {
            user: null
        };
    },
    props: {
        company: Object,
    },
    methods: {
        getSelectedRunner(company) {
            const app = this;
            let company_token = company.token
            axios.post('/api/selected-runner?company_token='+ company_token, {}).then(response => {
                // console.log(response.data.orders);
                app.user = response.data.user;
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
