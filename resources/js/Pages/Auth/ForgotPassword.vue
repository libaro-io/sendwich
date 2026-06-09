<script setup>
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/ui/Input.vue';
import BreezeLabel from '@/Components/ui/Label.vue';
import BreezeValidationErrors from '@/Components/ui/ValidationErrors.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Forgot Password" />

        <div class="form-hint">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </div>

        <div v-if="status" class="form-status">
            {{ status }}
        </div>

        <BreezeValidationErrors />

        <form @submit.prevent="submit">
            <div class="form-field">
                <BreezeLabel for="email" value="Email" />
                <BreezeInput id="email" type="email" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="form-actions form-actions--end">
                <button type="submit" class="chunk chunk--teal" :disabled="form.processing">
                    Email Password Reset Link
                </button>
            </div>
        </form>
    </BreezeGuestLayout>
</template>