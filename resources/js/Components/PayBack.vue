<template>
    <input type="checkbox" id="modal-payback" class="modal-toggle" v-bind:checked="isModalOpen"/>
    <div class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 v-if="user" class="font-bold text-lg">You current balance is € {{ user.dept }}</h3>
            <table>
                <thead>
                <tr>
                    <td>Runner</td>
                    <td>{{ user.dept > 0 ? 'Pays you' : 'You pay' }}</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in payBackList">
                    <td>{{ user.name }}</td>
                    <td>€ {{ user.paysBack }}</td>
                </tr>
                <tr>
                    <td><b>Your new balance</b></td>
                    <td><b>€{{ calculatedDept }}</b></td>
                </tr>
                </tbody>
            </table>
            <div class="alert alert-info shadow-lg text-white">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Clicking the "Everything is paid" button will edit the  <br> Sendwich Balance and send an email to all users involved.</span>
                </div>
            </div>
            <div class="modal-action">
                <p></p>
                <label for="modal-payback" class="btn">Maybe next time</label>
                <label class="btn btn-success" @click="handlePayouts">Everything is paid</label>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {useToast} from "vue-toastification";


const toast = useToast();
export default {
    name: "PayBack",
    mounted() {
        if (this.user.dept > 0) {
            this.buildPayBackList();
        } else {
            this.buildGiveBackList();
        }
    },
    data() {
        return {
            payBackList: [],
            counter: 0,
            calculatedDept: 0,
            isModalOpen: false
        };
    },
    props: {
        user: Object,
        users: Array,
    },
    methods: {
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
                document.getElementById('modal-payback').checked = false;
                toast.success(response.data.message);
            }).catch(error => {
                console.log(error);
            });
        },
    }
}
</script>

<style scoped>

</style>
