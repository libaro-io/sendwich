<script setup lang="ts">
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/ui/input-component.vue';
import BreezeLabel from '@/Components/ui/label-component.vue';
import BreezeValidationErrors from '@/Components/ui/validation-errors-component.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    })
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Confirm Password" />

        <div class="form-hint">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <BreezeValidationErrors />

        <form @submit.prevent="submit">
            <div class="form-field">
                <BreezeLabel for="password" value="Password" />
                <BreezeInput id="password" type="password" v-model="form.password" required autocomplete="current-password" autofocus />
            </div>

            <div class="form-actions form-actions--end">
                <button type="submit" class="chunk chunk--teal" :disabled="form.processing">
                    Confirm
                </button>
            </div>
        </form>
    </BreezeGuestLayout>
</template>

<style scoped>
@import "@css/pages/auth/confirmpassword.css";
</style>
