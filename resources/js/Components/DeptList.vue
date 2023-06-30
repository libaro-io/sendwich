<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2>Runners</h2>
            <table v-if="userCount" class="mt-5 table w-full">
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
            <a v-else class="alert alert-success shadow-md hover:shadow-lg cursor-pointer" :href="route('company.show')">
                <div>
                    <div>
                        <h3 class="font-bold">You are all alone here</h3>
                        <div class="">Start inviting colleagues and friends to get yourself started</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</template>

<script>

import PayBack from "@/Components/PayBack.vue";

export default {
    name: "DeptList",
    components: {PayBack},

    props: {
        company: Object,
        users: Array,
        runner:Object,
        simulated:Boolean,
        userCount: Number,
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
    }
}
</script>

<style scoped>

</style>
