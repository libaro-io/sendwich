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
// let products =ref( props.store.products);
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

                                        <button class="btn btn-danger ml-2" @click="remove(product)">Delete</button>
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
                                        <button class="btn btn-secondary" @click="saveNew(newProduct)">New</button>
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
