<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Invited users</h3>
            <div class="mt-5">
                <div class="rounded-md mb-1 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                    Email
                                </label>
                                <input v-model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="text" placeholder="Email">
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6 sm:flex-shrink-0">
                        <button type="button"
                                @click="sendInvite()"
                                class="bg-green-500 text-white inline-flex items-center px-4 py-2 shadow-sm font-medium rounded-md text-gray-700 hover:bg-green-100 sm:text-sm mr-1">
                            Invite
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Invite",
    mounted() {
    },
    props: {
        companyId: Number,
    },
    data() {
        return {
            email: "",
        };
    },
    methods: {
        sendInvite() {
            axios.post('/api/invites/send', {
                email: this.email,
                company_id: this.companyId,
            }).then(response => {
                console.log(response);
            }).catch(error => {
                console.log(error);
            });
        },
    }
}
</script>

<style scoped>

</style>
