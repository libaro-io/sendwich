<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/vue3';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
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
        <div class="page">
            <div class="page-container store-products">
                <!-- Store Details Card -->
                <div class="panel store-products__card">
                    <h2 class="panel-title">Store Details</h2>
                    <div class="store-products__grid">
                        <div class="store-products__field">
                            <label class="field-label">Name</label>
                            <input type="text" placeholder="Name" class="field-input" v-model="storeForm.name"/>
                        </div>
                        <div class="store-products__field">
                            <label class="field-label">Address</label>
                            <input type="text" placeholder="Address" class="field-input" v-model="storeForm.address"/>
                        </div>
                        <div class="store-products__field">
                            <label class="field-label">ZIP</label>
                            <input type="text" placeholder="ZIP" class="field-input" v-model="storeForm.zip"/>
                        </div>
                        <div class="store-products__field">
                            <label class="field-label">City</label>
                            <input type="text" placeholder="City" class="field-input" v-model="storeForm.city"/>
                        </div>
                        <div class="store-products__field">
                            <label class="field-label">Phone</label>
                            <input type="text" placeholder="Phone" class="field-input" v-model="storeForm.phone"/>
                        </div>
                        <div class="store-products__field">
                            <label class="field-label">Email</label>
                            <input type="email" placeholder="Email" class="field-input" v-model="storeForm.email"/>
                        </div>
                        <div class="store-products__field store-products__full">
                            <label class="field-label">Website</label>
                            <input type="text" placeholder="Website" class="field-input" v-model="storeForm.website"/>
                        </div>
                    </div>
                    <div class="form-actions form-actions--end">
                        <button class="chunk chunk--teal" @click="saveStore">Save Store Details</button>
                    </div>
                </div>

                <!-- Menu Card -->
                <div class="panel store-products__card">
                    <h2 class="panel-title">Menu — {{ store.name }}</h2>
                    <div class="store-products__scroll">
                        <table class="table-brut store-products__table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="product in store.products" :key="product.id">
                                <td><input type="text" v-model="product.name" class="field-input"/></td>
                                <td><input type="text" v-model="product.description" class="field-input"/></td>
                                <td>
                                    <div class="store-products__price-group">
                                        <input type="number" v-model="product.price" min="0" @keydown="preventNegative"
                                               class="field-input store-products__price-input"/>
                                        <select v-model="product.variable_price" class="field-select store-products__price-type">
                                            <option :value="false">Fixed</option>
                                            <option :value="true">Variable</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="store-products__row-actions">
                                        <button class="chunk chunk--teal chunk--sm" @click="save(product)">Save</button>
                                        <button class="icon-btn icon-btn--danger" title="Delete" @click="remove(product)">
                                            <FontAwesomeIcon icon="fa-solid fa-xmark"/>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="text" placeholder="Name for new product" v-model="newProduct.name" class="field-input"/>
                                </td>
                                <td>
                                    <input type="text" placeholder="Description for new product" v-model="newProduct.description" class="field-input"/>
                                </td>
                                <td>
                                    <div class="store-products__price-group">
                                        <input type="number" v-model="newProduct.price" placeholder="0.00" min="0" @keydown="preventNegative"
                                               class="field-input store-products__price-input"/>
                                        <select v-model="newProduct.variable_price" class="field-select store-products__price-type">
                                            <option :value="false">Fixed</option>
                                            <option :value="true">Variable</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <button class="chunk chunk--sun chunk--sm" @click="saveNew(newProduct)">Add</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<style scoped>
@import "@css/pages/store/products.css";
</style>