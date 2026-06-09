<script setup>
import UsersWithOrders from "@/Pages/Display/sections/UsersWithOrders.vue";
import SelectedRunner from "@/Pages/Display/sections/SelectedRunner.vue";

const props = defineProps({
    deliveryMoment: String,
    company: Object
});
</script>

<template>
    <div class="display-page brand-canvas">
        <div class="display-page__body">
            <div class="page">
                <div class="page-container">
                    <div class="display-page__grid">
                        <div>
                            <UsersWithOrders :delivery-moment="deliveryMoment" :company="company" />
                        </div>
                        <div>
                            <SelectedRunner :company="company" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "@css/pages/display.css";
</style>
