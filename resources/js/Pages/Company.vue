<script setup>

import Authenticated from "@/Layouts/Authenticated.vue";
import {useForm, usePage} from '@inertiajs/inertia-vue3';
import {onMounted, ref, watch } from 'vue';


const props = defineProps({
    user: Object,
    company: Object,
    users : Array,
});

const toggle = ref(false);
const deptstoggle = ref(false);

const form = useForm({
    name: null,
    email: null,
})


const invite = ()=> {
    form.post(route('invite'),{
        onSuccess: () => toggle.value = false,
    });

}

const deleteForm = useForm({});

const deleteUser = (userId)=> {
    deleteForm.delete(route('users.destroy',userId),{preserveScroll:true});
}

const page = usePage();

onMounted(()=>{

})

</script>
<template>
    <Authenticated>
        <div class="min-h-screen">
            <div class="font-sans text-gray-900 antialiased">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-white shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h2 class="text-3xl leading-6 font-medium text-gray-900 mb-5 font-bold">{{ company.name }}</h2>
                                    <section class="ml-4">
                                        <div class="flex justify-between items-center">
                                            <h3 class="text-xl mb-3 pl-2 align-middle">{{ users.length }} Users</h3>
                                            <label
                                                for="create-modal"
                                                class="btn btn-success modal-button mb-3"
                                            >
                                                Invite new user
                                            </label>
                                            <input type="checkbox" id="create-modal" class="modal-toggle" v-model="toggle"/>
                                            <label for="create-modal" class="modal modal-bottom sm:modal-middle cursor-pointer">
                                                <label class="modal-box relative" for="">
                                                    <form @submit.prevent="invite">
                                                        <h3 class="text-lg font-bold mb-4">Invite new user</h3>
                                                        <div class="grid-cols-1 space-y-4">
                                                            <input type="text"
                                                                   v-model="form.name"
                                                                   placeholder="name"
                                                                   class="input input-bordered input-sm w-full max-w-xs"
                                                            />
                                                            <div v-if="form.errors.name">{{ form.errors.name }}</div>
                                                            <input type="email"
                                                                   v-model="form.email"
                                                                   placeholder="email"
                                                                   class="input input-bordered input-sm w-full max-w-xs"
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
                                            <input type="checkbox" id="delete-error-modal" class="modal-toggle" v-model="deptstoggle"/>
                                            <label for="delete-error-modal" class="modal modal-bottom sm:modal-middle cursor-pointer">
                                                <label class="modal-box relative" for="">
                                                    <form @submit.prevent="invite">
                                                        <h3 class="text-lg font-bold mb-4">Invite new user</h3>
                                                        <div class="grid-cols-1 space-y-4">
                                                            <input type="text"
                                                                   v-model="form.name"
                                                                   placeholder="name"
                                                                   class="input input-bordered input-sm w-full max-w-xs"
                                                            />
                                                            <div v-if="form.errors.name">{{ form.errors.name }}</div>
                                                            <input type="email"
                                                                   v-model="form.email"
                                                                   placeholder="email"
                                                                   class="input input-bordered input-sm w-full max-w-xs"
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
                                                    <th>Depts</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="user in users">
                                                    <td>{{user.name}}</td>
                                                    <td>
                                                        {{user.email}}
                                                    </td>
                                                    <td>
                                                        &euro; {{user.depts}}
                                                    </td>
                                                    <td>
                                                        <form @submit.prevent="deleteUser(user.id)">
                                                            <button type="submit" class="btn btn-sm btn-outline">
                                                                Delete
                                                            </button >
                                                        </form>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
