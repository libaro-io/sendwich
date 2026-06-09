<script setup>
import BreezeCheckbox from '@/Components/ui/Checkbox.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/ui/Input.vue';
import BreezeLabel from '@/Components/ui/Label.vue';
import BreezeTitle from '@/Components/ui/Title.vue';
import BreezeValidationErrors from '@/Components/ui/ValidationErrors.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    companyName: '',
    terms: false,
});

const submit = () => {
    form.post(route('company.register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Register"/>
        <BreezeValidationErrors />
        <form @submit.prevent="submit">
            <BreezeTitle value="About your company or group" />
            <div class="form-field">
                <BreezeLabel for="companyName" value="Name"/>
                <BreezeInput id="companyName" type="text" v-model="form.companyName" required autofocus autocomplete="companyName"/>
            </div>
            <BreezeTitle class="form-title--spaced" value="About you" />
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
                <BreezeLabel for="password_confirm" value="Confirm Password"/>
                <BreezeInput id="password_confirmation" type="password" v-model="form.password_confirmation" required autocomplete="new-password"/>
            </div>

            <div class="form-check">
                <BreezeCheckbox id="terms" v-model:checked="form.terms" required />
                <label for="terms" class="form-check-label">
                    I accept the <a :href="route('legal.general')" target="_blank" class="form-link">terms and conditions</a>
                </label>
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