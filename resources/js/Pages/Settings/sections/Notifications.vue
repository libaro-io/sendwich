<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import axios from 'axios';
import Modal from '@/Components/ui/modal-component.vue';
import type { Company, NotificationChannel, NotificationDriver } from '@interfaces/settings';

interface DayLabel {
    value: number;
    label: string;
}

type ChannelFormData = Omit<NotificationChannel, 'id'>;

const props = defineProps<{
    company: Company;
    availableDrivers: NotificationDriver[];
}>();

const toast = useToast();

const dayLabels: DayLabel[] = [
    {value: 1, label: 'Mon'},
    {value: 2, label: 'Tue'},
    {value: 3, label: 'Wed'},
    {value: 4, label: 'Thu'},
    {value: 5, label: 'Fri'},
    {value: 6, label: 'Sat'},
    {value: 0, label: 'Sun'},
];

const notificationForm = useForm({
    reminder_enabled: props.company.reminder_enabled ?? false,
    reminder_time: props.company.reminder_time?.substring(0, 5) ?? '',
    reminder_days: props.company.reminder_days ?? [1, 2, 3, 4, 5],
    notification_channels: (props.company.notification_channels ?? []).map((ch): NotificationChannel => ({
        id: ch.id,
        driver: ch.driver,
        configuration: ch.configuration ?? {webhook_url: ''},
        enabled: ch.enabled,
    })),
});

const toggleDay = (day: number): void => {
    const idx = notificationForm.reminder_days.indexOf(day);
    if (idx === -1) {
        notificationForm.reminder_days.push(day);
    } else {
        notificationForm.reminder_days.splice(idx, 1);
    }
};

const showChannelModal = ref(false);
const editingChannelIndex = ref<number | null>(null);
const channelForm = ref<ChannelFormData>({
    driver: props.availableDrivers[0]?.value ?? 'google_chat',
    configuration: {webhook_url: ''},
    enabled: true,
});

const openAddChannel = (): void => {
    editingChannelIndex.value = null;
    channelForm.value = {
        driver: props.availableDrivers[0]?.value ?? 'google_chat',
        configuration: {webhook_url: ''},
        enabled: true,
    };
    showChannelModal.value = true;
};

const openEditChannel = (index: number): void => {
    editingChannelIndex.value = index;
    const ch = notificationForm.notification_channels[index];
    channelForm.value = {
        driver: ch.driver,
        configuration: {...ch.configuration},
        enabled: ch.enabled,
    };
    showChannelModal.value = true;
};

const saveChannel = (): void => {
    if (editingChannelIndex.value !== null) {
        const ch = notificationForm.notification_channels[editingChannelIndex.value];
        ch.driver = channelForm.value.driver;
        ch.configuration = {...channelForm.value.configuration};
        ch.enabled = channelForm.value.enabled;
    } else {
        notificationForm.notification_channels.push({
            id: null,
            driver: channelForm.value.driver,
            configuration: {...channelForm.value.configuration},
            enabled: channelForm.value.enabled,
        });
    }
    showChannelModal.value = false;
};

const removeChannel = (index: number): void => {
    notificationForm.notification_channels.splice(index, 1);
};

const testingChannelIndex = ref<number | null>(null);

const testChannel = async (index: number): Promise<void> => {
    const channel = notificationForm.notification_channels[index];

    if (!channel.id) {
        toast.warning("Please save the channel before testing.");
        return;
    }

    testingChannelIndex.value = index;

    try {
        const response = await axios.post(route('settings.notifications.channels.test', {channel: channel.id}));
        toast.success(response.data.message);
    } catch (e: any) {
        toast.error(e.response?.data?.message ?? "Failed to send test notification.");
    } finally {
        testingChannelIndex.value = null;
    }
};

const driverLabel = (driverValue: string): string => {
    return props.availableDrivers.find(d => d.value === driverValue)?.label ?? driverValue;
};

const saveNotifications = (): void => {
    notificationForm.post(route('settings.notifications.update'), {
        onSuccess: () => toast.success("Notification settings saved"),
    });
};
</script>

<template>
    <form @submit.prevent="saveNotifications">
        <div class="panel">
            <h3 class="settings__section-title">Notifications</h3>

            <!-- Channels -->
            <div class="settings__block">
                <div class="settings__block-head">
                    <div>
                        <h4 class="settings__label">Channels</h4>
                        <p class="settings__desc">
                            Configure the channels used to deliver notifications such as order reminders.
                        </p>
                    </div>
                    <button type="button" @click="openAddChannel" class="chunk chunk--teal chunk--sm">
                        + Add Channel
                    </button>
                </div>

                <div v-if="notificationForm.notification_channels.length === 0" class="settings__empty">
                    No notification channels configured. Add one to start receiving notifications.
                </div>

                <div v-else-if="notificationForm.notification_channels.every(ch => !ch.enabled)"
                     class="callout callout--warning settings__callout">
                    All channels are disabled. Notifications won't be delivered until at least one channel is enabled.
                </div>

                <div v-for="(channel, index) in notificationForm.notification_channels"
                     :key="index"
                     class="panel panel--flat settings__channel"
                >
                    <div class="settings__channel-info">
                        <input type="checkbox" v-model="channel.enabled" class="switch settings__switch-sm" />
                        <span class="settings__label">{{ driverLabel(channel.driver) }}</span>
                    </div>
                    <div class="settings__channel-actions">
                        <button type="button"
                                @click="testChannel(index)"
                                class="chunk chunk--ghost chunk--sm"
                                :disabled="!channel.id || testingChannelIndex === index"
                        >
                            <span v-if="testingChannelIndex !== index">Test</span>
                            <span v-else>Testing...</span>
                        </button>
                        <button type="button" @click="openEditChannel(index)" class="chunk chunk--ghost chunk--sm">
                            Edit
                        </button>
                        <button type="button" @click="removeChannel(index)" class="chunk chunk--ghost chunk--sm settings__btn-danger">
                            Remove
                        </button>
                    </div>
                </div>
            </div>

            <div class="settings__divider"></div>

            <!-- Notification types -->
            <h4 class="settings__subtitle">Active Notifications</h4>

            <!-- Order Reminder -->
            <div>
                <div class="settings__row-start">
                    <div>
                        <h4 class="settings__label">Order Reminder</h4>
                        <p class="settings__desc">
                            Send a daily reminder to your configured notification channels prompting everyone to place their sandwich orders.
                        </p>
                    </div>
                    <input type="checkbox" v-model="notificationForm.reminder_enabled" class="switch" />
                </div>

                <div v-if="notificationForm.reminder_enabled" class="settings__detail">
                    <div>
                        <label class="field-label">Reminder time</label>
                        <input type="time" v-model="notificationForm.reminder_time" class="field-input settings__time" />
                        <div v-if="notificationForm.errors.reminder_time" class="field-error">{{ notificationForm.errors.reminder_time }}</div>
                    </div>

                    <div>
                        <label class="field-label">Days</label>
                        <div class="settings__days">
                            <button v-for="day in dayLabels"
                                    :key="day.value"
                                    type="button"
                                    @click="toggleDay(day.value)"
                                    class="chunk chunk--sm"
                                    :class="notificationForm.reminder_days.includes(day.value) ? 'chunk--teal' : 'chunk--cream'"
                            >
                                {{ day.label }}
                            </button>
                        </div>
                        <div v-if="notificationForm.errors.reminder_days" class="field-error">{{ notificationForm.errors.reminder_days }}</div>
                    </div>
                </div>
            </div>

            <div class="settings__save settings__save--bordered">
                <button type="submit" class="chunk chunk--teal" :disabled="notificationForm.processing">Save</button>
            </div>
        </div>
    </form>

    <!-- Channel modal -->
    <Modal :open="showChannelModal" :title="editingChannelIndex !== null ? 'Edit Channel' : 'Add Channel'" @close="showChannelModal = false">
        <div class="settings__modal-form">
            <div>
                <span class="field-label">Driver</span>
                <select v-model="channelForm.driver" class="field-select">
                    <option v-for="driver in availableDrivers"
                            :key="driver.value"
                            :value="driver.value"
                    >
                        {{ driver.label }}
                    </option>
                </select>
            </div>

            <div>
                <span class="field-label">Webhook URL</span>
                <input type="url"
                       v-model="channelForm.configuration.webhook_url"
                       placeholder="https://chat.googleapis.com/v1/spaces/..."
                       class="field-input"
                />
            </div>
        </div>

        <template #actions>
            <button type="button" @click="saveChannel" class="chunk chunk--teal">
                {{ editingChannelIndex !== null ? 'Update' : 'Add' }}
            </button>
            <button type="button" @click="showChannelModal = false" class="chunk chunk--ghost">Cancel</button>
        </template>
    </Modal>
</template>

<style scoped>
@import "@css/pages/settings/sections/notifications.css";
</style>