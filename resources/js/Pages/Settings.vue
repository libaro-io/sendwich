<script setup>

import Authenticated from "@/Layouts/Authenticated.vue";
import { useForm } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';


const props = defineProps({
    user: Object,
    company: Object,
});

const toggle = ref(false);

const form = useForm({
    name: null,
    email: null,
})

const submit= (type, user) =>{
    Inertia.post(route('settings.update'), {
        company: this.props.company,
    },{
        preserveState:true,
        replace :true,
        only:['company']
    });
};

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
                                    <h1>Settings</h1>
                                        <div class="overflow-x-auto mb-5">
                                            <div class="grid grid-cols-2 justify-items-start mt-3">
                                                <div class="form-control">
                                                    <label class="label">
                                                        <span class="label-text">Name *</span>
                                                    </label>
                                                    <input type="text" required placeholder="Libaro" class="input input-bordered w-full max-w-xs"
                                                           v-model="company.name"/>
                                                </div>
                                                <div class="form-control">
                                                    <label class="label">
                                                        <span class="label-text">Select runner at</span>
                                                    </label>
                                                    <input type="time" class="input input-bordered w-full max-w-xs"
                                                           v-model="company.select_runner_at"/>
                                                </div>
                                                <div class="form-control mt-5">
                                                    <label class="btn btn-success" @click="submit">Save</label>
                                                </div>
                                            </div>
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
