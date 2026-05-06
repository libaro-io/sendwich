<script setup>

import Authenticated from "@/Layouts/Authenticated.vue";
import {useForm} from '@inertiajs/inertia-vue3';
import {useToast} from "vue-toastification";
import {ref} from "vue";

const toast = useToast();

const props = defineProps({
    user: Object,
    company: Object,
    availableDrivers: Array,
});

const dayLabels = [
    {value: 1, label: 'Mon'},
    {value: 2, label: 'Tue'},
    {value: 3, label: 'Wed'},
    {value: 4, label: 'Thu'},
    {value: 5, label: 'Fri'},
    {value: 6, label: 'Sat'},
    {value: 0, label: 'Sun'},
];

const runnerForm = useForm({
    time: props.company.select_runner_at?.substring(0, 5) ?? '',
});

const notificationForm = useForm({
    reminder_enabled: props.company.reminder_enabled ?? false,
    reminder_time: props.company.reminder_time?.substring(0, 5) ?? '',
    reminder_days: props.company.reminder_days ?? [1, 2, 3, 4, 5],
    notification_channels: (props.company.notification_channels ?? []).map(ch => ({
        id: ch.id,
        driver: ch.driver,
        configuration: ch.configuration ?? {webhook_url: ''},
        enabled: ch.enabled,
    })),
});

const toggleDay = (day) => {
    const idx = notificationForm.reminder_days.indexOf(day);
    if (idx === -1) {
        notificationForm.reminder_days.push(day);
    } else {
        notificationForm.reminder_days.splice(idx, 1);
    }
};

const showChannelModal = ref(false);
const editingChannelIndex = ref(null);
const channelForm = ref({
    driver: props.availableDrivers[0]?.value ?? 'google_chat',
    configuration: {webhook_url: ''},
    enabled: true,
});

const openAddChannel = () => {
    editingChannelIndex.value = null;
    channelForm.value = {
        driver: props.availableDrivers[0]?.value ?? 'google_chat',
        configuration: {webhook_url: ''},
        enabled: true,
    };
    showChannelModal.value = true;
};

const openEditChannel = (index) => {
    editingChannelIndex.value = index;
    const ch = notificationForm.notification_channels[index];
    channelForm.value = {
        driver: ch.driver,
        configuration: {...ch.configuration},
        enabled: ch.enabled,
    };
    showChannelModal.value = true;
};

const saveChannel = () => {
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

const removeChannel = (index) => {
    notificationForm.notification_channels.splice(index, 1);
};

const driverLabel = (driverValue) => {
    return props.availableDrivers.find(d => d.value === driverValue)?.label ?? driverValue;
};

const saveRunner = () => {
    runnerForm.post(route('settings.runner.update'), {
        onSuccess: () => toast.success("Runner settings saved"),
    });
};

const saveNotifications = () => {
    notificationForm.post(route('settings.notifications.update'), {
        onSuccess: () => toast.success("Notification settings saved"),
    });
};

</script>
<template>
    <Authenticated>
        <div class="min-h-screen">
            <div class="font-sans text-gray-900 antialiased">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-4">

                            <h1 class="m-0">Settings</h1>

                            <!-- Runner selection time -->
                            <form @submit.prevent="saveRunner">
                                <div class="bg-white shadow sm:rounded-lg">
                                    <div class="px-4 py-5 sm:p-6">
                                        <h3 class="text-lg font-bold mb-4">
                                            At which time of day should the runner be selected?</h3>

                                        <div class="grid grid-cols-5 space-y-4">
                                            <input type="time" required
                                                   v-model="runnerForm.time"
                                                   placeholder="time"
                                                   class="input input-bordered max-w-2xl mt-10"
                                            />
                                            <div class="chat chat-start col-span-4 -mt-4">
                                                <div class="chat-bubble chat-bubble-primary text-white">The selected runner will be notified by email and assigned to collect orders placed before a set time. Orders placed later will remain unassigned or can be manually claimed by a runner until the next day's scheduled run.</div>
                                            </div>
                                            <div v-if="runnerForm.errors.time" class="text-error text-sm">{{ runnerForm.errors.time }}</div>
                                        </div>

                                        <div class="flex justify-end mt-4">
                                            <button type="submit"
                                                    class="btn btn-success"
                                                    :disabled="runnerForm.processing"
                                            >Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- Notifications -->
                            <form @submit.prevent="saveNotifications">
                                <div class="bg-white shadow sm:rounded-lg">
                                    <div class="px-4 py-5 sm:p-6">
                                        <h3 class="text-lg font-bold mb-6">Notifications</h3>

                                        <!-- Channels -->
                                        <div class="mb-8">
                                            <div class="flex justify-between items-center mb-4">
                                                <div>
                                                    <h4 class="font-semibold">Channels</h4>
                                                    <p class="text-gray-500 text-sm mt-1">
                                                        Configure the channels used to deliver notifications such as order reminders.
                                                    </p>
                                                </div>
                                                <button type="button" @click="openAddChannel" class="btn btn-sm btn-outline btn-primary">
                                                    + Add Channel
                                                </button>
                                            </div>

                                            <div v-if="notificationForm.notification_channels.length === 0" class="text-gray-500 text-sm italic">
                                                No notification channels configured. Add one to start receiving notifications.
                                            </div>

                                            <div v-else-if="notificationForm.notification_channels.every(ch => !ch.enabled)"
                                                 class="alert alert-warning text-sm mb-3">
                                                All channels are disabled. Notifications won't be delivered until at least one channel is enabled.
                                            </div>

                                            <div v-for="(channel, index) in notificationForm.notification_channels"
                                                 :key="index"
                                                 class="border rounded-lg p-4 mb-3 flex items-center justify-between"
                                            >
                                                <div class="flex items-center gap-3">
                                                    <input type="checkbox"
                                                           v-model="channel.enabled"
                                                           class="toggle toggle-success toggle-sm"
                                                    />
                                                    <span class="font-semibold">{{ driverLabel(channel.driver) }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button"
                                                            @click="openEditChannel(index)"
                                                            class="btn btn-sm btn-ghost"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button type="button"
                                                            @click="removeChannel(index)"
                                                            class="btn btn-sm btn-ghost text-error"
                                                    >
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="divider"></div>

                                        <!-- Notification types -->
                                        <h4 class="font-semibold mb-4">Active Notifications</h4>

                                        <!-- Order Reminder -->
                                        <div>
                                            <div class="flex justify-between items-center mb-4">
                                                <div>
                                                    <h4 class="font-semibold">Order Reminder</h4>
                                                    <p class="text-gray-500 text-sm mt-1">
                                                        Send a daily reminder to your configured notification channels prompting everyone to place their sandwich orders.
                                                    </p>
                                                </div>
                                                <input type="checkbox"
                                                       v-model="notificationForm.reminder_enabled"
                                                       class="toggle toggle-success"
                                                />
                                            </div>

                                            <div v-if="notificationForm.reminder_enabled" class="space-y-6">
                                                <div>
                                                    <label class="label"><span class="label-text font-semibold">Reminder time</span></label>
                                                    <input type="time"
                                                           v-model="notificationForm.reminder_time"
                                                           class="input input-bordered w-full max-w-xs"
                                                    />
                                                    <div v-if="notificationForm.errors.reminder_time" class="text-error text-sm mt-1">{{ notificationForm.errors.reminder_time }}</div>
                                                </div>

                                                <div>
                                                    <label class="label"><span class="label-text font-semibold">Days</span></label>
                                                    <div class="flex flex-wrap gap-2">
                                                        <button v-for="day in dayLabels"
                                                                :key="day.value"
                                                                type="button"
                                                                @click="toggleDay(day.value)"
                                                                class="btn btn-sm"
                                                                :class="notificationForm.reminder_days.includes(day.value) ? 'btn-primary' : 'btn-outline'"
                                                        >
                                                            {{ day.label }}
                                                        </button>
                                                    </div>
                                                    <div v-if="notificationForm.errors.reminder_days" class="text-error text-sm mt-1">{{ notificationForm.errors.reminder_days }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex justify-end mt-6">
                                            <button type="submit"
                                                    class="btn btn-success"
                                                    :disabled="notificationForm.processing"
                                            >Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Channel modal -->
                        <input type="checkbox" id="channel-modal" class="modal-toggle" v-model="showChannelModal"/>
                        <label for="channel-modal" class="modal modal-bottom sm:modal-middle cursor-pointer">
                            <label class="modal-box relative" for="">
                                <h3 class="text-lg font-bold mb-4">
                                    {{ editingChannelIndex !== null ? 'Edit Channel' : 'Add Channel' }}
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="label"><span class="label-text font-semibold">Driver</span></label>
                                        <select v-model="channelForm.driver"
                                                class="select select-bordered w-full"
                                        >
                                            <option v-for="driver in availableDrivers"
                                                    :key="driver.value"
                                                    :value="driver.value"
                                            >
                                                {{ driver.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="label"><span class="label-text font-semibold">Webhook URL</span></label>
                                        <input type="url"
                                               v-model="channelForm.configuration.webhook_url"
                                               placeholder="https://chat.googleapis.com/v1/spaces/..."
                                               class="input input-bordered w-full"
                                        />
                                    </div>

                                </div>

                                <div class="modal-action">
                                    <label for="channel-modal" class="btn btn-ghost">Cancel</label>
                                    <button type="button" @click="saveChannel" class="btn btn-primary">
                                        {{ editingChannelIndex !== null ? 'Update' : 'Add' }}
                                    </button>
                                </div>
                            </label>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
