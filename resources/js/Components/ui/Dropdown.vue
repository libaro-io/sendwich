<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        default: 'right'
    },
    width: {
        default: '48'
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const open = ref(false);
</script>

<template>
    <div class="dropdown">
        <div @click="open = ! open">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="dropdown__overlay" @click="open = false"></div>

        <transition name="dropdown">
            <div
                v-show="open"
                class="dropdown__menu"
                :class="[`dropdown__menu--w${width}`, `dropdown__menu--${align}`]"
                style="display: none;"
                @click="open = false"
            >
                <div class="dropdown__panel">
                    <slot name="content" />
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
@import "@css/components/ui/dropdown.css";
</style>