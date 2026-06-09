<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/vue3';
import axios from "axios";
import {useToast} from "vue-toastification";
import {reactive} from "vue";

const toast = useToast();

const props = defineProps({
    store: Object,
});

const storeForm = reactive({
    name: props.store?.name || '',
    address: props.store?.address || '',
    zip: props.store?.zip || '',
    city: props.store?.city || '',
    phone: props.store?.phone || '',
    email: props.store?.email || '',
    website: props.store?.website || '',
});

const saveStore = () => {
    if (!storeForm.name) {
        toast.error('Fill in a name');
        return;
    }
    axios.post('/api/store/' + props.store.id, {
        store: storeForm,
    }).then(response => {
        toast.success(response.data.message);
        props.store.name = response.data.store.name;
    }).catch(error => {
        console.error(error);
        toast.error('Failed to update store details');
    });
};

const resetNewProduct = () => {
    newProduct = {
        name: '',
        description: '',
        price: 0,
        variable_price: false,
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

const preventNegative = (event) => {
    if (event.key === '-') event.preventDefault();
};

let newProduct = reactive();
resetNewProduct();
</script>
<template>
    <Head title="Stores"/>
    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Store Details Card -->
                <div class="card bg-white shadow-xl mb-8">
                    <div class="card-body">
                        <h2 class="card-title text-xl font-bold mb-4">Store Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div class="form-control flex flex-col">
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Name</span>
                                </label>
                                <input type="text" placeholder="Name" class="input input-bordered w-full"
                                       v-model="storeForm.name"/>
                            </div>
                            <div class="form-control flex flex-col">
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Address</span>
                                </label>
                                <input type="text" placeholder="Address" class="input input-bordered w-full"
                                       v-model="storeForm.address"/>
                            </div>
                            <div class="form-control flex flex-col">
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">ZIP</span>
                                </label>
                                <input type="text" placeholder="ZIP" class="input input-bordered w-full"
                                       v-model="storeForm.zip"/>
                            </div>
                            <div class="form-control flex flex-col">
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">City</span>
                                </label>
                                <input type="text" placeholder="City" class="input input-bordered w-full"
                                       v-model="storeForm.city"/>
                            </div>
                            <div class="form-control flex flex-col">
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Phone</span>
                                </label>
                                <input type="text" placeholder="Phone" class="input input-bordered w-full"
                                       v-model="storeForm.phone"/>
                            </div>
                            <div class="form-control flex flex-col">
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Email</span>
                                </label>
                                <input type="email" placeholder="Email" class="input input-bordered w-full"
                                       v-model="storeForm.email"/>
                            </div>
                            <div class="form-control flex flex-col md:col-span-2">
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Website</span>
                                </label>
                                <input type="text" placeholder="Website" class="input input-bordered w-full"
                                       v-model="storeForm.website"/>
                            </div>
                        </div>
                        <div class="card-actions justify-end mt-6">
                            <button class="btn btn-primary px-6" @click="saveStore">Save Store Details</button>
                        </div>
                    </div>
                </div>

                <!-- Menu Card -->
                <div class="card bg-white shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-xl font-bold mb-4">Menu — {{ store.name }}</h2>
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
                                        <div class="flex">
                                            <input type="number" v-model="product.price"
                                                   min="0" @keydown="preventNegative"
                                                   class="input input-bordered rounded-r-none w-24"/>
                                            <select v-model="product.variable_price"
                                                    class="select select-bordered rounded-l-none border-l-0">
                                                <option :value="false">Fixed</option>
                                                <option :value="true">Variable</option>
                                            </select>
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
                                    <th>
                                        <input type="text" placeholder="Name for new product" v-model="newProduct.name"
                                               class="input  input-bordered b w-full max-w-xs"/>
                                    </th>
                                    <td><input type="text" placeholder="Description for new product"
                                               v-model="newProduct.description" class="input  input-bordered w-full max-w-xs"/>
                                    </td>
                                    <td>
                                        <div class="flex">
                                            <input type="number" v-model="newProduct.price"
                                                   placeholder="0.00" min="0" @keydown="preventNegative"
                                                   class="input input-bordered rounded-r-none w-24"/>
                                            <select v-model="newProduct.variable_price"
                                                    class="select select-bordered rounded-l-none border-l-0">
                                                <option :value="false">Fixed</option>
                                                <option :value="true">Variable</option>
                                            </select>
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
