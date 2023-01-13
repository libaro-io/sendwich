<template>
    <input type="checkbox" id="create-store-modal" class="modal-toggle"/>
    <div class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Create new Store</h3>
            <div class="grid grid-cols-2 justify-items-center mt-3">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Name *</span>
                    </label>
                    <input type="text" required placeholder="Libaro" class="input input-bordered w-full max-w-xs"
                           v-model="newStore.name"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Address</span>
                    </label>
                    <input type="text" placeholder="Vaartdijkstraat 19" class="input input-bordered w-full max-w-xs"
                           v-model="newStore.address"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">ZIP</span>
                    </label>
                    <input type="text" placeholder="8200" class="input input-bordered w-full max-w-xs"
                           v-model="newStore.zip"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">City</span>
                    </label>
                    <input type="text" placeholder="Brugge" class="input input-bordered w-full max-w-xs"
                           v-model="newStore.city"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone</span>
                    </label>
                    <input type="text" placeholder="+32 477 11 22 33" class="input input-bordered w-full max-w-xs"
                           v-model="newStore.phone"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="text" placeholder="info@libaro.be" class="input input-bordered w-full max-w-xs"
                           v-model="newStore.email"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Website</span>
                    </label>
                    <input type="text" placeholder="https://libaro.be" class="input input-bordered w-full max-w-xs"
                           v-model="newStore.website"/>
                </div>
            </div>
            <div class="modal-action">
                <label for="create-store-modal" class="btn btn-danger">Close</label>
                <label class="btn btn-success" @click="submit">Create</label>
            </div>
        </div>
    </div>
</template>


<script setup>
import {Head} from '@inertiajs/inertia-vue3';
import axios from "axios";
import {reactive} from "vue";
import {useToast} from "vue-toastification";

const toast = useToast();

let newStore = reactive({
    name: '',
    address: '',
    zip: '',
    city: '',
    phone: '',
    email: '',
    website: '',
});

const submit = () => {
        axios.put('/api/store/add', {
            store: newStore,
        }).then(response => {
            toast.success(response.data.message);
            this.emitter.emit('updateStores');
        }).catch(error => {
            console.error(error);
        });
};
</script>
<style scoped>

</style>
