<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/vue3';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import List from "@/Pages/Store/sections/List.vue";
import ListItem from "@/Pages/Store/sections/ListItem.vue";
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
        <div class="app-page">
            <div class="page">
                <div class="page-container">
                    <div class="stores__grid">
                        <List title="Stores">
                            <template v-slot:actions>
                                <button type="button" @click="newStore = true" class="chunk chunk--teal">
                                    <FontAwesomeIcon icon="fa-solid fa-store" class="stores__btn-icon"/>
                                    Create new Store
                                </button>
                            </template>
                            <template v-slot:main>
                                <ListItem v-for="store in stores" :item="store" type="store" :editable="true"></ListItem>
                            </template>
                         </List>
                    </div>
                </div>
            </div>
        </div>
        <NewStore :open="newStore" @close="newStore = false"></NewStore>
    </BreezeAuthenticatedLayout>
</template>

<style scoped>
@import "@css/pages/store/stores.css";
</style>
