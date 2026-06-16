<script setup lang="ts">
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/ui/input-component.vue';
import BreezeLabel from '@/Components/ui/label-component.vue';
import BreezeTitle from '@/Components/ui/title-component.vue';
import BreezeValidationErrors from '@/Components/ui/validation-errors-component.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

const props = defineProps({
    companyLink: String,
    companyName: String
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    company_link: props.companyLink,
    terms: false,
});

const submit = () => {
    form.post(route('register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Register"/>

        <BreezeValidationErrors />
        <BreezeTitle :value="companyName" />
        <form @submit.prevent="submit">
            <div class="form-field">
                <BreezeLabel for="name" value="Name"/>
                <BreezeInput id="name" type="text" v-model="form.name" required autofocus autocomplete="name"/>
            </div>

            <div class="form-field">
                <BreezeLabel for="email" value="Email"/>
                <BreezeInput id="email" type="email" v-model="form.email" required autocomplete="username"/>
            </div>

            <div class="form-field">
                <BreezeLabel for="password" value="Password"/>
                <BreezeInput id="password" type="password" v-model="form.password" required autocomplete="new-password"/>
            </div>

            <div class="form-field">
                <BreezeLabel for="password_confirmation" value="Confirm Password"/>
                <BreezeInput id="password_confirmation" type="password" v-model="form.password_confirmation" required autocomplete="new-password"/>
            </div>

            <div class="form-actions">
                <Link :href="route('login')" class="form-link">
                    Already registered?
                </Link>
                <button type="submit" class="chunk chunk--teal" :disabled="form.processing">
                    Register
                </button>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
