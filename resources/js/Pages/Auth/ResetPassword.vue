<script setup>
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/ui/Input.vue';
import BreezeLabel from '@/Components/ui/Label.vue';
import BreezeValidationErrors from '@/Components/ui/ValidationErrors.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Reset Password" />

        <BreezeValidationErrors />

        <form @submit.prevent="submit">
            <div class="form-field">
                <BreezeLabel for="email" value="Email" />
                <BreezeInput id="email" type="email" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="form-field">
                <BreezeLabel for="password" value="Password" />
                <BreezeInput id="password" type="password" v-model="form.password" required autocomplete="new-password" />
            </div>

            <div class="form-field">
                <BreezeLabel for="password_confirmation" value="Confirm Password" />
                <BreezeInput id="password_confirmation" type="password" v-model="form.password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="form-actions form-actions--end">
                <button type="submit" class="chunk chunk--teal" :disabled="form.processing">
                    Reset Password
                </button>
            </div>
        </form>
    </BreezeGuestLayout>
</template>