<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import List from "@/Components/List.vue";
import ListItem from "@/Components/ListItem.vue";
import NewStore from "@/Pages/Store/newStore.vue";
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
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-4">
                    <List title="Stores">
                        <div class="">
                            <label for="create-store-modal" class="btn btn-success modal-button">Create new
                                Store</label>
                        </div>
                        <ListItem v-for="store in stores" :item="store" type="store" :editable="true"></ListItem>
                    </List>
                </div>
            </div>
        </div>
        <NewStore></NewStore>
    </BreezeAuthenticatedLayout>
</template>
