<script setup lang="ts">
import {ref, watch} from "vue";
import {router} from "@inertiajs/vue3";
import {debounce} from "lodash";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import type {Store} from '@interfaces/dashboard';

defineProps<{
    stores: Store[];
}>();

defineEmits<{
    selectStore: [store: Store];
}>();

const search = ref('');

watch(search, debounce((value: string) => {
    router.get(route('dashboard'), {search: value}, {
        preserveState: true,
        replace: true,
    });
}, 300));
</script>
<template>
    <div class="panel">
        <h2 class="panel-title">Menu</h2>
        <div v-if="stores && stores.length" class="menu__list">
            <div v-for="(store, index) in stores"
                 :key="index"
                 class="panel panel--flat menu__row">
                <div class="menu__info">
                    <div class="menu__name">{{ store.name }}</div>
                    <p class="menu__count" v-if="store.order_count">Orders: {{ store.order_count }}</p>
                </div>
                <div>
                    <button
                        @click="$emit('selectStore', store)"
                        type="button"
                        class="chunk chunk--teal chunk--sm menu__btn">
                        Products
                        <span class="tag tag--sun tag--bold">{{store.products_count}}</span>
                    </button>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="empty-action">
                <a :href="route('store.index')" class="chunk chunk--teal">
                    <FontAwesomeIcon icon="fa-solid fa-store" class="menu__icon" />
                    Create your first store
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "@css/pages/dashboard/sections/menu.css";
</style>
