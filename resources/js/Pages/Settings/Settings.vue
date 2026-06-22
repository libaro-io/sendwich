<script setup lang="ts">
import Authenticated from '@/Layouts/Authenticated.vue';
import Runner from './sections/Runner.vue';
import Notifications from './sections/Notifications.vue';
import type { Company, NotificationDriver } from '@interfaces/settings';

defineProps<{
    company: Company;
    availableDrivers: NotificationDriver[];
}>();
</script>

<template>
    <Authenticated>
        <div class="app-page">
            <div class="page">
                <div class="page-container">
                    <div class="settings__grid">
                        <h1 class="settings__title">Settings</h1>

                        <Runner :company="company" />

                        <Notifications :company="company" :available-drivers="availableDrivers" />
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<style scoped>
@import "@css/pages/settings.css";
</style>