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
                app.simulatedRunner = response.data.runner;
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>
<template>
    <div class="panel selected-runner selected-runner--muted" v-if="runner">
        <div>
            <h2 class="selected-runner__name">{{ runner.name }}</h2>
            <h1 class="selected-runner__role selected-runner__role--accent">You are the chosen one</h1>
        </div>
    </div>
    <div class="panel selected-runner" v-else-if="simulatedRunner">
        <div>
            <h2 class="selected-runner__name">{{ simulatedRunner.name }}</h2>
            <h1 class="selected-runner__role selected-runner__role--muted">Is in lead to be selected as runner</h1>
        </div>
    </div>
</template>

<style scoped>
@import "@css/pages/display/sections/selected-runner.css";
</style>