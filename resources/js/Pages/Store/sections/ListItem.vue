<script setup lang="ts">
defineProps<{
    item: { id: number; name: string };
    editable?: boolean;
    type: string;
}>();
</script>
<template>
    <div class="panel panel--flat list-row">
        <div class="list-row__main">
            <div>
                <div class="list-row__name">
                    {{ item.name }}
                </div>
            </div>
            <a :href="route(type + '.show', item.id)" v-if="editable" class="chunk chunk--teal chunk--sm">
                Edit
            </a>
        </div>
    </div>
</template>

<style scoped>
@import "@css/pages/store/sections/list-item.css";
</style>
