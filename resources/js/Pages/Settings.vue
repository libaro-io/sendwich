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
                                    <div class="flex items-center justify-between mb-5">
                                        <h1 class="m-0">Settings</h1>
                                    </div>
                                    <div class="overflow-x-auto mb-5">
                                        <form @submit.prevent="save">
                                            <h3 class="text-lg font-bold mb-4">
                                                At which time of day should the runner be selected?</h3>
                                            <div class="grid-cols-1 space-y-4">
                                                <input type="time" required
                                                       v-model="form.time"
                                                       placeholder="time"
                                                       class="input input-bordered w-full"
                                                />
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
