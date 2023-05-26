<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeTitle from '@/Components/Title.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';


const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    companyName: '',
    terms: false,
});



const submit = () => {
    console.log('Form data',form.data);
    form.post(route('company.register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Register"/>
        <BreezeValidationErrors class="mb-4"/>
        <form @submit.prevent="submit">
            <BreezeTitle value="About your company or group"></BreezeTitle>
            <div>
                <BreezeLabel for="companyName" value="Company or group name"/>
                <BreezeInput id="companyName" type="text" class="mt-1 block w-full" v-model="form.companyName" required autofocus
                             autocomplete="companyName"/>
            </div>
            <BreezeTitle class="mt-4" value="About you"></BreezeTitle>
            <div>
                <BreezeLabel for="name" value="Name"/>
                <BreezeInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                             autocomplete="name"/>
            </div>

            <div class="mt-4">
                <BreezeLabel for="email" value="Email"/>
                <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                             autocomplete="username"/>
            </div>

            <div class="mt-4">
                <BreezeLabel for="password" value="Password"/>
                <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                             autocomplete="new-password"/>
            </div>

            <div class="mt-4">
                <BreezeLabel for="password_confirm" value="Confirm Password"/>
                <BreezeInput id="password_confirmation" type="password" class="mt-1 block w-full"
                             v-model="form.password_confirmation" required autocomplete="new-password"/>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Already registered?
                </Link>
                <BreezeLabel/>
                <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
