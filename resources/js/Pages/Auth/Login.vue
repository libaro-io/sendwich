<script setup>
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/ui/Input.vue';
import BreezeLabel from '@/Components/ui/Label.vue';
import BreezeValidationErrors from '@/Components/ui/ValidationErrors.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Log in" />

        <BreezeValidationErrors />

        <div v-if="status" class="form-status">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="form-field">
                <BreezeLabel for="email" value="Email" />
                <BreezeInput id="email" type="email" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="form-field">
                <BreezeLabel for="password" value="Password" />
                <BreezeInput id="password" type="password" v-model="form.password" required autocomplete="current-password" />
            </div>

            <div class="form-actions">
                <Link v-if="canResetPassword" :href="route('password.request')" class="form-link">
                    Forgot your password?
                </Link>

                <button type="submit" class="chunk chunk--teal" :disabled="form.processing">
                    Log in
                </button>
            </div>
        </form>
    </BreezeGuestLayout>
</template>