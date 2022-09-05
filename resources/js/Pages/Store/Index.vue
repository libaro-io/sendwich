<script setup>
import mitt from 'mitt'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import List from "@/Components/List.vue";
import ListItem from "@/Components/ListItem.vue";

const props = defineProps({
    stores: Object,
    company: Object,
});

</script>
<template>
    <Head title="Stores"/>
    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-4">
                    <List title="Stores">
                        <ListItem v-for="store in stores" :item="store" type="store" :editable="true"></ListItem>
                    </List>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
