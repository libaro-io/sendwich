<script setup lang="ts">
import {ref, onMounted} from "vue";
import axios from "axios";
import {emitter} from "@/Composables/emitter";
import type {DisplayCompany, Runner} from '@interfaces/display';

const props = defineProps<{
    company: DisplayCompany;
}>();

const runner = ref<Runner | null>(null);
const simulatedRunner = ref<Runner | null>(null);

const getSimulatedRunner = (): void => {
    const company_token = props.company.token;
    axios.post(route('order.simulated-runner', {company_token}), {}).then(response => {
        simulatedRunner.value = response.data.runner;
    }).catch(error => {
        console.log(error);
    });
};

const getSelectedRunner = (): void => {
    const company_token = props.company.token;
    axios.post(route('order.selected-runner', {company_token}), {}).then(response => {
        runner.value = response.data.runner;
        if (!runner.value) {
            getSimulatedRunner();
        }
    }).catch(error => {
        console.log(error);
    });
};

onMounted(() => {
    setInterval(() => {
        getSelectedRunner();
    }, 60 * 1000);
    getSelectedRunner();
    emitter.on("updateSelectedRunner", getSelectedRunner);
});
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