<script setup lang="ts">
import {ref, onMounted} from "vue";
import axios from "axios";
import {useToast} from "vue-toastification";
import {router} from "@inertiajs/vue3";
import {useHelpers} from "@/Composables/helpers";
import Modal from "@/Components/ui/modal-component.vue";
import type {User} from '@interfaces/dashboard';

const props = defineProps<{
    user: User;
    users: User[];
    open: boolean;
}>();

const emit = defineEmits<{
    close: [];
}>();

const {formatMoney} = useHelpers();
const toast = useToast();

const payBackList = ref<User[]>([]);
const counter = ref(0);
const calculatedDept = ref(0);

const buildGiveBackList = (): void => {
    calculatedDept.value = props.user.dept * -1;
    counter.value = props.users.length - 1;
    while (calculatedDept.value > 0) {
        const user = props.users[counter.value];
        if (user.dept > 0) {
            const paybackUserDept = user.dept;
            payBackList.value.push(user);
            const paysBack = calculatedDept.value > paybackUserDept ? paybackUserDept : calculatedDept.value;
            user.paysBack = paysBack;
            calculatedDept.value -= paysBack;
        }
        counter.value--;
    }
};

const buildPayBackList = (): void => {
    calculatedDept.value = props.user.dept;
    for (let i = counter.value; i < props.users.length && calculatedDept.value > 0; i++) {
        const user = props.users[i];
        if (user.dept < 0) {
            const paybackUserDept = -user.dept;
            payBackList.value.push(user);
            const paysBack = Math.min(paybackUserDept, calculatedDept.value);
            user.paysBack = paysBack;
            calculatedDept.value -= paysBack;
        }
    }
};

const handlePayouts = (): void => {
    axios.post('/api/payouts/handle', {
        payouts: payBackList.value,
    }).then(response => {
        emit('close');
        toast.success(response.data.message);
        router.reload();
    }).catch(error => {
        console.log(error);
    });
};

onMounted(() => {
    if (props.user.dept > 0) {
        buildPayBackList();
    } else {
        buildGiveBackList();
    }
});
</script>
<template>
    <Modal :open="open" :title="user ? `Balance: ${formatMoney(user.dept)}` : 'Payback details'" @close="$emit('close')">
        <table class="table-brut payback__table">
            <thead>
            <tr>
                <th>Runner</th>
                <th class="payback__num">{{ user.dept > 0 ? 'Pays you' : 'You pay' }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="u in payBackList" :key="u.id">
                <td class="payback__name">{{ u.name }}</td>
                <td class="payback__amount">{{ formatMoney(u.paysBack) }}</td>
            </tr>
            <tr class="payback__total-row">
                <td class="payback__total-label">Your new balance</td>
                <td class="payback__total-value">{{ formatMoney(calculatedDept) }}</td>
            </tr>
            </tbody>
        </table>

        <div class="callout callout--info payback__note">
            Clicking the "Everything is paid" button will edit the Sendwich Balance and send an email to all users involved.
        </div>

        <template #actions>
            <button class="chunk chunk--teal" @click="handlePayouts">Everything is paid</button>
            <button class="chunk chunk--ghost" @click="$emit('close')">Maybe next time</button>
        </template>
    </Modal>
</template>

<style scoped>
@import "@css/pages/dashboard/sections/pay-back.css";
</style>
