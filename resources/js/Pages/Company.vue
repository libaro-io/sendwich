<script setup>

import Authenticated from "@/Layouts/Authenticated.vue";
import { useForm } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';


const props = defineProps({
    user: Object,
    company: Object,
    users : Array,
});

const toggle = ref(false);

const form = useForm({
    name: null,
    email: null,
})

const invite = ()=> {
    form.post(route('invite'),{
        onSuccess: () => toggle.value = false,
    });
}

const togglePermission= (type, user) =>{
    Inertia.post(route('user.permissions'), {
        user_id: user.id,
        type: type,
    },{
        preserveState:true,
        replace :true,
        only:['users']
    });
};

const deleteUser = (user) => {
    if(confirm("Do you really want to delete this user?")) {
        Inertia.post(route('user.delete'), {
            user_id: user.id,
        }, {
            preserveState: true,
            replace: true,
            only: ['users']
        });
    }
}

</script>
<template>
    <Authenticated>
        <div class="min-h-screen">
            <div class="font-sans text-gray-900 antialiased">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-white shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6 relative">
                                    <h1>Users</h1>
                                    <label for="create-modal" class="modal-button action-button shadow-md">
                                        Invite new user
                                    </label>
                                        <div class="flex justify-between items-center">
                                            <input type="checkbox" id="create-modal" class="modal-toggle" v-model="toggle"/>
                                            <label for="create-modal" class="modal modal-bottom sm:modal-middle cursor-pointer">
                                                <label class="modal-box relative" for="">
                                                    <form @submit.prevent="invite">
                                                        <h3 class="text-lg font-bold mb-4">Invite new user</h3>
                                                        <div class="grid-cols-1 space-y-4">
                                                            <input type="text"
                                                                   v-model="form.name"
                                                                   placeholder="name"
                                                                   class="input input-bordered w-full"
                                                            />
                                                            <div v-if="form.errors.name">{{ form.errors.name }}</div>
                                                            <input type="email"
                                                                   v-model="form.email"
                                                                   placeholder="email"
                                                                   class="input input-bordered w-full"
                                                            />
                                                            <div v-if="form.errors.email">{{ form.errors.email }}</div>
                                                        </div>
                                                        <div class="flex justify-end mt-4">
                                                            <button type="submit"
                                                                    class="btn btn-success"
                                                                    :disabled="form.processing"
                                                            >Send Invitation</button>
                                                        </div>
                                                    </form>
                                                </label>
                                            </label>
                                        </div>
                                        <div class="overflow-x-auto mb-5">
                                            <table class="table w-full">
                                                <!-- head -->
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Edit stores</th>
                                                    <th>Edit company</th>
                                                    <th>Remove</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="user in users">
                                                    <td>{{user.name}}</td>
                                                    <td>
                                                        {{user.email}}
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" v-model="user.canEditStore" class="checkbox" @change="togglePermission('edit-store', user)" />
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" v-model="user.canEditCompany" class="checkbox" @change="togglePermission('edit-company', user)" />
                                                    </td>
                                                    <td >
                                                        <button v-if="$page.props.auth.user.id !== user.id" class="btn btn-accent btn-circle btn-xs" @click="deleteUser(user)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
