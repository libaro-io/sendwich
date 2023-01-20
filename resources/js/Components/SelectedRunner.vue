<template>
    <div class="shadow sm:rounded-lg px-4 py-5 bg-white-50" v-if="runner">
        <div>
            <h2 class="text-center">{{ runner.name }}</h2>
            <h1 class="text-center">You are the chosen one</h1>
        </div>
    </div>
    <div class="shadow sm:rounded-lg px-4 py-5 bg-white-fix" v-else-if="simulatedRunner">
        <div>
            <h2 class="text-center">{{ simulatedRunner.name }}</h2>
            <h1 class="text-center">Is in lead to be selected as runner</h1>
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
        this.emitter.on("updateSelectedRunner", this.getSelectedRunner)
    },
    data() {
        return {
            runner: null,
            simulatedRunner: null
        };
    },
    props: {
        company: Object,
    },
    methods: {
        getSelectedRunner(company) {
            console.log("getSelectedRunner", company);
            const app = this;
            let company_token = company.token
            axios.post('/api/selected-runner?company_token=' + company_token, {}).then(response => {
                app.runner = response.data.runner;
                if (!app.runner) {
                    app.getSimulatedRunner(this.company);
                }
            }).catch(error => {
                console.log(error);
            });
        },
        getSimulatedRunner(company) {
            const app = this;
            let company_token = company.token
            axios.post('/api/simulated-runner?company_token=' + company_token, {}).then(response => {
                // console.log(response.data.orders);
                app.simulatedRunner = response.data.runner;
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
