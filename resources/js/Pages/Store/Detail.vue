<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import axios from "axios";

const props = defineProps({
    store: Object,
});

const save = (product) => {
    axios.post('/api/store' + product.id, {
        product: product,
    }).then(response => {
        toast.success(response.data.message);
        this.emitter.emit('updateProducts');
    }).catch(error => {
        console.error(error);
    });
}

</script>
<template>
    <Head title="Stores"/>
    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="card-title">{{ store.name }}</div>
                        <div class="overflow-x-auto">
                            <table class="table w-full bg-base-200">
                                <!-- head -->
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="product in store.products">
                                    <th><input type="text" :value="product.name" class="input w-full max-w-xs"/></th>
                                    <td><input type="text" :value="product.description" class="input w-full max-w-xs"/>
                                    </td>
                                    <td>
                                        <div class="form-control">
                                            <label class="input-group">
                                                <input type="number" :value="product.price"
                                                       class="input input-bordered"/>
                                                <span>EUR</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" @click="save(product)">Save</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
