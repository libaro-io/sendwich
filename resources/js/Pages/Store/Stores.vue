<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import List from "@/Components/List.vue";
import ListItem from "@/Components/ListItem.vue";
import NewStore from "@/Pages/Store/NewStore.vue";
import {ref} from "vue";

const props = defineProps({
    stores: Object,
    company: Object,
});

let newStore = ref(false);

</script>
<template>
    <Head title="Stores"/>
    <BreezeAuthenticatedLayout>
        <div class="min-h-screen">
            <div class="font-sans text-gray-900 antialiased">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-4">
                            <List title="Stores">
                                <template v-slot:actions>
                                    <label for="create-store-modal" class="action-button modal-button">Create new Store</label>
                                </template>
                                <template v-slot:main>
                                    <ListItem v-for="store in stores" :item="store" type="store"
                                              :editable="true"></ListItem>
                                </template>
                            </List>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <NewStore></NewStore>
    </BreezeAuthenticatedLayout>
</template>
