<script>
import axios from "axios";
import {useToast} from "vue-toastification";
import {useHelpers} from "@/Composables/helpers";
import Modal from "@/Components/ui/modal-component.vue";
import {router} from "@inertiajs/vue3";

const helper = useHelpers();
const toast = useToast();
export default {
    name: "PayBack",
    components: {
        Modal,
    },
    mounted() {
        if (this.user.dept > 0) {
            this.buildPayBackList();
        } else {
            this.buildGiveBackList();
        }
    },
    props: {
        user: Object,
        users: Array,
        open: Boolean,
    },
    emits: ["close"],
    data() {
        return {
            payBackList: [],
            counter: 0,
            calculatedDept: 0,
        };
    },
    methods: {
        formatMoney: helper.formatMoney,
        buildGiveBackList() {
            this.calculatedDept = this.user.dept * -1;
            this.counter = this.users.length - 1;
            while (this.calculatedDept > 0) {
                const user = this.users[this.counter];
                if (user.dept > 0) {
                    const paybackUserDept = user.dept
                    this.payBackList.push(user);
                    if (this.calculatedDept > paybackUserDept) {
                        user.paysBack = paybackUserDept;
                    } else {
                        user.paysBack = this.calculatedDept
                    }
                    this.calculatedDept -= user.paysBack;
                }
                this.counter--;
            }
        },
        buildPayBackList() {
            this.calculatedDept = this.user.dept;
            for (let i = this.counter; i < this.users.length && this.calculatedDept > 0; i++) {
                const user = this.users[i];
                if (user.dept < 0) {
                    const paybackUserDept = -user.dept;
                    this.payBackList.push(user);
                    user.paysBack = Math.min(paybackUserDept, this.calculatedDept);
                    this.calculatedDept -= user.paysBack;
                }
            }
        },
        handlePayouts() {
            const app = this;
            axios.post('/api/payouts/handle', {
                'payouts': this.payBackList
            }).then(response => {
                app.$emit('close');
                toast.success(response.data.message);
                router.reload();
            }).catch(error => {
                console.log(error);
            });
        },
    }
}
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
