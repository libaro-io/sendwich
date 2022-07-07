<template>
    <div class="shadow sm:rounded-lg px-4 py-5 bg-cyan-100" v-if="user">
        <div>
            <h3 class="text-md leading-6 text-cyan-600 text-center">{{ user.name }}</h3>
            <h3 class="text-lg leading-6 text-cyan-600 text-center mt-2">You are the chosen one</h3>
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
            this.getSelectedRunner();
        }, 60 * 1000);
        this.getSelectedRunner();
        this.emitter.on("updateSelectedRunner", this.getSelectedRunner())
    },
    data() {
        return {
            user: null
        };
    },
    props: {
    },
    methods: {
        getSelectedRunner() {
            const app = this;
            axios.post('/api/selected-runner', {}).then(response => {
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
