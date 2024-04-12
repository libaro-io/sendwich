<script setup>

import Authenticated from "@/Layouts/Authenticated.vue";
import {useForm} from '@inertiajs/inertia-vue3';
import {useToast} from "vue-toastification";

const toast = useToast();

const props = defineProps({
    user: Object,
    company: Object,
});


const form = useForm({
    name: props.company.name,
    time: props.company.select_runner_at,
})

const save = () => {
    form.post(route('settings.update'), {
        onSuccess: () => toast.success("Time saved"),
    });
}

</script>
<template>
    <Authenticated>
        <div class="min-h-screen">
            <div class="font-sans text-gray-900 antialiased">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-white shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6 relative">
                                    <div class="flex flex-col items-center justify-between mb-5">
                                        <h1 class="m-0 w-full">Settings</h1>

                                    </div>
                                    <div class="overflow-x-auto mb-5">
                                        <form @submit.prevent="save">
                                            <h3 class="text-lg font-bold mb-4">
                                                At which time of day should the runner be selected?</h3>

                                            <div class="grid grid-cols-5 space-y-4">
                                                <input type="time" required
                                                       v-model="form.time"
                                                       placeholder="time"
                                                       class="input input-bordered max-w-2xl mt-6"
                                                />
                                                <div class="chat chat-start col-span-4 -mt-4">
                                                    <div class="chat-bubble chat-bubble-primary text-primary-content">The selected runner will be notified by email and assigned to collect orders placed before a set time. Orders placed later will remain unassigned or can be manually claimed by a runner until the next day’s scheduled run.</div>
                                                </div>

                                                <div v-if="form.errors.time">{{ form.errors.time }}</div>
                                            </div>

                                            <div class="flex justify-end mt-4">
                                                <button type="submit"
                                                        class="btn btn-success"
                                                        :disabled="form.processing"
                                                >Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
