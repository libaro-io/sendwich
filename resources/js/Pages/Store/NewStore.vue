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
                           v-model="NewStore.name"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Address</span>
                    </label>
                    <input type="text" placeholder="Vaartdijkstraat 19" class="input input-bordered w-full max-w-xs"
                           v-model="NewStore.address"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">ZIP</span>
                    </label>
                    <input type="text" placeholder="8200" class="input input-bordered w-full max-w-xs"
                           v-model="NewStore.zip"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">City</span>
                    </label>
                    <input type="text" placeholder="Brugge" class="input input-bordered w-full max-w-xs"
                           v-model="NewStore.city"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone</span>
                    </label>
                    <input type="text" placeholder="+32 477 11 22 33" class="input input-bordered w-full max-w-xs"
                           v-model="NewStore.phone"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="text" placeholder="info@libaro.be" class="input input-bordered w-full max-w-xs"
                           v-model="NewStore.email"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Website</span>
                    </label>
                    <input type="text" placeholder="https://libaro.be" class="input input-bordered w-full max-w-xs"
                           v-model="NewStore.website"/>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Get started quickly</span>
                    </label>
                    <select v-model="NewStore.template" class="select select-primary w-full max-w-xs">
                        <option value="">Create an empty store</option>
                        <option value="sandwich">Add typical sandwiches</option>
                        <option value="pasta">Add pasta's</option>
                        <option value="pasta">Add French Fries products</option>
                    </select>
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
import axios from "axios";
import {reactive} from "vue";
import {useToast} from "vue-toastification";
import {Inertia} from "@inertiajs/inertia";

const toast = useToast();

let NewStore = reactive({
    name: '',
    address: '',
    zip: '',
    city: '',
    phone: '',
    email: '',
    website: '',
    template: '',
});

const submit = () => {
    if(NewStore.name){
        axios.put('/api/store/add', {
            store: NewStore,
        }).then(response => {
            // toast.success(response.data.message);
            Inertia.get('/stores',{}, {});
        }).catch(error => {
            console.error(error);
        });
    }else{
        toast.success('Fill in a name');
    }

};
</script>
<style scoped>

</style>
