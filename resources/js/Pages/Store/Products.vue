<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import axios from "axios";
import {useToast} from "vue-toastification";
import {reactive} from "vue";

const toast = useToast();

const props = defineProps({
    store: Object,
});

const resetNewProduct = () => {
    newProduct = {
        name: '',
        description: '',
        price: 0,
    };
}

const save = (product) => {
    axios.post('/api/store/product/' + product.id, {
        product: product,
    }).then(response => {
        toast.success(response.data.message);
    }).catch(error => {
        console.error(error);
    });
}

const remove = (product) => {
    axios.delete('/api/store/product/' + product.id).then(response => {
        console.log(response.data);
        props.store.products = response.data.products;
        toast.success(response.data.message);
    }).catch(error => {
        console.error(error);
    });
}

const saveNew = (product) => {
    axios.put('/api/store/product', {
        product: product,
        store_id: props.store.id
    }).then(response => {
        props.store.products = response.data.products;
        toast.success(response.data.message);
        resetNewProduct()
    }).catch(error => {
        console.error(error);
    });
}

let newProduct = reactive();
resetNewProduct();
</script>
<template>
    <Head title="Stores"/>
    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card bg-white shadow-xl">
                    <div class="card-body">
                        <div class="card-title">{{ store.name }}</div>
                        <div class="overflow-x-auto">
                            <table class="table w-full rounded-md">
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
                                    <th><input type="text" v-model="product.name" class="input w-full max-w-xs"/></th>
                                    <td><input type="text" v-model="product.description" class="input w-full max-w-xs"/>
                                    </td>
                                    <td>
                                        <div class="form-control">
                                            <label class="input-group">
                                                <input type="number" v-model="product.price"
                                                       class="input input-bordered"/>
                                                <span>EUR</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" @click="save(product)">Save</button>
                                        <button class="btn btn-accent btn-circle ml-2" @click="remove(product)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
<!--                                        <button class="btn btn-danger ml-2" @click="remove(product)">Delete</button>-->
                                    </td>
                                </tr>

                                <tr>
                                    <th><input type="text" placeholder="Name for new product" v-model="newProduct.name"
                                               class="input w-full max-w-xs"/></th>
                                    <td><input type="text" placeholder="Description for new product"
                                               v-model="newProduct.description" class="input w-full max-w-xs"/>
                                    </td>
                                    <td>
                                        <div class="form-control">
                                            <label class="input-group">
                                                <input type="number" v-model="newProduct.price"
                                                       placeholder="0.00" class="input input-bordered"/>
                                                <span>EUR</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-secondary" @click="saveNew(newProduct)">Add</button>
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
