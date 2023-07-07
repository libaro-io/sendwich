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
