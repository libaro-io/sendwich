<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from "@headlessui/vue";

defineProps<{
    open: boolean;
    title?: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const handleClose = () => {
    emit('close');
}
</script>
<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="modal-root" @close="handleClose">
            <!-- Backdrop transition -->
            <TransitionChild
                as="template"
                enter="modal-trans-enter"
                enter-from="modal-fade-from"
                enter-to="modal-fade-to"
                leave="modal-trans-leave"
                leave-from="modal-fade-to"
                leave-to="modal-fade-from"
            >
                <div class="modal-backdrop" />
            </TransitionChild>

            <div class="modal-scroll">
                <div class="modal-center">
                    <TransitionChild
                        as="template"
                        enter="modal-trans-enter"
                        enter-from="modal-panel-from"
                        enter-to="modal-panel-to"
                        leave="modal-trans-leave"
                        leave-from="modal-panel-to"
                        leave-to="modal-panel-from"
                    >
                        <DialogPanel class="modal-panel">
                            <div class="panel modal-card">
                                <!-- Close button in top-right -->
                                <button
                                    type="button"
                                    class="modal-close"
                                    @click="handleClose"
                                ><FontAwesomeIcon icon="fa-solid fa-xmark" class="modal-close-icon" />
                                </button>

                                <div class="modal-body">
                                    <DialogTitle
                                        v-if="title"
                                        as="h3"
                                        class="modal-title"
                                    >
                                        {{ title }}
                                    </DialogTitle>

                                    <!-- Content slot -->
                                    <div class="modal-content">
                                        <slot />
                                    </div>
                                </div>

                                <!-- Actions slot (footer buttons) -->
                                <div v-if="$slots.actions" class="modal-actions">
                                    <slot name="actions" />
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
<style scoped>
@import "@css/components/ui/modal-component.css";
</style>
