<script setup lang="ts">
import axios from "axios";
import {reactive} from "vue";
import {useToast} from "vue-toastification";
import {router} from "@inertiajs/vue3";
import Modal from "@/Components/ui/modal-component.vue";
import type {NewStoreForm} from '@interfaces/store';

const toast = useToast();

defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    close: [];
}>();

const NewStore = reactive<NewStoreForm>({
    name: '',
    address: '',
    zip: '',
    city: '',
    phone: '',
    email: '',
    website: '',
    template: '',
});

const submit = (): void => {
    if(NewStore.name){
        axios.put(route('store.add'), {
            store: NewStore,
        }).then(response => {
            toast.success(response.data.message);
            emit('close');
            router.get(route('store.show', response.data.store.id), {}, {});
        }).catch(error => {
            console.error(error);
        });
    }else{
        toast.error('Fill in a name');
    }
};
</script>
<template>
    <Modal :open="open" title="Create new Store" @close="$emit('close')">
        <form id="create-store-form" @submit.prevent="submit">
            <div class="new-store__grid">
                <div>
                    <label class="field-label">Name *</label>
                    <input type="text" required placeholder="Libaro" class="field-input" v-model="NewStore.name"/>
                </div>
                <div>
                    <label class="field-label">Address</label>
                    <input type="text" placeholder="Vaartdijkstraat 19" class="field-input" v-model="NewStore.address"/>
                </div>
                <div>
                    <label class="field-label">ZIP</label>
                    <input type="text" placeholder="8200" class="field-input" v-model="NewStore.zip"/>
                </div>
                <div>
                    <label class="field-label">City</label>
                    <input type="text" placeholder="Brugge" class="field-input" v-model="NewStore.city"/>
                </div>
                <div>
                    <label class="field-label">Phone</label>
                    <input type="text" placeholder="+32 477 11 22 33" class="field-input" v-model="NewStore.phone"/>
                </div>
                <div>
                    <label class="field-label">Email</label>
                    <input type="text" placeholder="info@libaro.be" class="field-input" v-model="NewStore.email"/>
                </div>
                <div>
                    <label class="field-label">Website</label>
                    <input type="text" placeholder="https://libaro.be" class="field-input" v-model="NewStore.website"/>
                </div>
                <div>
                    <label class="field-label">Get started quickly</label>
                    <select v-model="NewStore.template" class="field-select">
                        <option value="">Create an empty store</option>
                        <option value="sandwich">Add typical sandwiches</option>
                        <option value="pasta">Add pasta's</option>
                        <option value="fries">Add French Fries products</option>
                    </select>
                </div>
            </div>
        </form>
        <template #actions>
            <button type="submit" form="create-store-form" class="chunk chunk--teal">Create</button>
            <button type="button" class="chunk chunk--ghost" @click="$emit('close')">Close</button>
        </template>
    </Modal>
</template>

<style scoped>
@import "@css/pages/store/new-store.css";
</style>
