<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import type { Company } from '@interfaces/settings';

const props = defineProps<{
    company: Company;
}>();

const toast = useToast();

const runnerForm = useForm({
    time: props.company.select_runner_at?.substring(0, 5) ?? '',
    auto_assign_runner: props.company.auto_assign_runner ?? true,
});

const saveRunner = (): void => {
    runnerForm.post(route('settings.runner.update'), {
        onSuccess: () => toast.success('Runner settings saved'),
    });
};
</script>

<template>
    <form @submit.prevent="saveRunner">
        <div class="panel">
            <h3 class="settings__section-title">
                At what time of day should the runner be selected?
            </h3>

            <div class="settings__runner-grid">
                <div>
                    <input type="time" required
                           v-model="runnerForm.time"
                           placeholder="time"
                           class="field-input settings__time"
                    />
                </div>
                <div class="panel panel--flat settings__hint">
                    The selected runner will be notified by email and assigned to collect orders placed before a set time. Orders placed later will remain unassigned or can be manually claimed by a runner until the next day's scheduled run.
                </div>
                <div v-if="runnerForm.errors.time" class="field-error settings__error-wide">{{ runnerForm.errors.time }}</div>
            </div>

            <div class="settings__row">
                <div>
                    <h4 class="settings__label">Auto-assign runner</h4>
                    <p class="settings__desc">
                        When disabled, the runner must always be assigned manually via the dashboard.
                    </p>
                </div>
                <input type="checkbox" v-model="runnerForm.auto_assign_runner" class="switch" />
            </div>

            <div class="settings__save">
                <button type="submit" class="chunk chunk--teal" :disabled="runnerForm.processing">Save</button>
            </div>
        </div>
    </form>
</template>

<style scoped>
@import "@css/pages/settings/sections/runner.css";
</style>