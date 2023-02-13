<template>
    <input type="checkbox" id="modal-payback" class="modal-toggle"/>
    <div class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 v-if="user" class="font-bold text-lg">You current balance is € {{ user.dept }}</h3>
            <table>
                <thead>
                <tr>
                    <td>Runner</td>
                    <td>{{user.dept > 0 ? 'Pays you' : 'You pay' }}</td>
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
            <div class="modal-action">
                <label for="modal-payback" class="btn">Maybe next time</label>
                <label class="btn btn-success" @click="handlePayouts">Everything is paid</label>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "PayBack",
    mounted() {
        if(this.user.dept >0){
            this.buildPayBackList();
        }else{
            this.buildGiveBackList();
        }
    },
    data() {
        return {
            payBackList: [],
            counter: 0,
            calculatedDept: 0
        };
    },
    props: {
        user: Object,
        users: Array,
    },
    methods: {
        buildGiveBackList(){
            this.calculatedDept = this.user.dept *-1;
            this.counter = this.users.length -1;
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
                    console.log(this.calculatedDept, user.paysBack)
                    this.calculatedDept -= user.paysBack;
                }
                this.counter--;
            }
        },
        buildPayBackList() {
            this.calculatedDept = this.user.dept;
            while (this.calculatedDept > 0) {
                const user = this.users[this.counter];
                if (user.dept < 0) {
                    const paybackUserDept = user.dept *-1
                    this.payBackList.push(user);
                    if (this.calculatedDept > paybackUserDept) {
                        user.paysBack = paybackUserDept;
                    } else {
                        user.paysBack = this.calculatedDept
                    }
                    this.calculatedDept -= user.paysBack;
                }
                this.counter++;
            }
        },
        // buildPayBackList() {
        //     this.calculatedDept = Math.abs(this.user.dept);
        //     this.counter = (this.user.dept > 0) ? 0 : this.users.length - 1;
        //
        //     while (this.calculatedDept > 0) {
        //         const user = this.users[this.counter];
        //         if ((this.user.dept > 0 && user.dept < 0) || (this.user.dept < 0 && user.dept > 0)) {
        //             const paybackUserDept = Math.abs(user.dept);
        //             this.payBackList.push(user);
        //             if (this.calculatedDept > paybackUserDept) {
        //                 user.paysBack = paybackUserDept;
        //             } else {
        //                 user.paysBack = this.calculatedDept;
        //             }
        //             this.calculatedDept -= user.paysBack;
        //         }
        //         this.counter += (this.user.dept > 0) ? 1 : -1;
        //     }
        // }
        handlePayouts() {
            const app = this;
            axios.post('/api/payouts/handle', {
                'payouts' : this.payBackList
            }).then(response => {
            }).catch(error => {
                console.log(error);
            });
        },
    }
}
</script>

<style scoped>

</style>
