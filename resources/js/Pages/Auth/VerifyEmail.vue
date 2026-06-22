<script setup lang="ts">
import { computed } from 'vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm();

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Email Verification" />

        <div class="form-hint">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div v-if="verificationLinkSent" class="form-status">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="form-actions">
                <button type="submit" class="chunk chunk--teal" :disabled="form.processing">
                    Resend Verification Email
                </button>

                <Link :href="route('logout')" method="post" as="button" class="form-link form-link--danger">Log Out</Link>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
