<script setup>
import mitt from 'mitt'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import Users from "@/Pages/Invites/Components/Users.vue";
import Invite from "@/Pages/Invites/Components/Invite.vue";

const props = defineProps({
    users: Array,
    companyId: Number,
});

</script>

<template>
    <Head title="Invites"/>
    <BreezeAuthenticatedLayout>
        <div class="pt-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-4">
                    <Invite :companyId="companyId"></Invite>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-4">
                    <Users :users="users"></Users>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
