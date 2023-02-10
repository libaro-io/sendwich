<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeTitle from '@/Components/Title.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';

const props = defineProps({
    companyLink: String,
    companyName: String,
    name:String,
    email:String,
    id:Number,
});

const form = useForm({
    name: props.name,
    email: props.email,
    id: props.id,
    password: '',
    password_confirmation: '',
    company_link: props.companyLink,
    terms: false,
});

const submit = () => {
    console.log('Form data',form.data);
    form.post(route('register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Register"/>
        <BreezeValidationErrors class="mb-4"/>
        <BreezeTitle :value="companyName"></BreezeTitle>
        <p> Hallo , {{ name }} </p>
        <p> Vul hieronder je wachtwoord in en je kunt bestellen. </p>
        <form @submit.prevent="submit">
            <div class="mt-4">
                <BreezeLabel for="password" value="Password"/>
                <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                             autocomplete="new-password"/>
            </div>

            <div class="mt-4">
                <BreezeLabel for="password_confirmation" value="Confirm Password"/>
                <BreezeInput id="password_confirmation" type="password" class="mt-1 block w-full"
                             v-model="form.password_confirmation" required autocomplete="new-password"/>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Al geregistreerd?
                </Link>
                <BreezeLabel/>
                <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Sign up
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
