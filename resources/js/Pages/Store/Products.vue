<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/vue3';
import axios from "axios";
import {useToast} from "vue-toastification";
import {reactive} from "vue";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';

const toast = useToast();

const props = defineProps({
    store: Object,
});

const resetNewProduct = () => {
    newProduct.name = '';
    newProduct.description = '';
    newProduct.price = 0;
    newProduct.variable_price = false;
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

let newProduct = reactive({
    name: '',
    description: '',
    price: 0,
    variable_price: false,
});
</script>
<template>
    <Head title="Stores"/>
    <BreezeAuthenticatedLayout>
        <div class="page">
            <div class="page-container">
                <div class="panel">
                    <h1 class="products-page__title">{{ store.name }}</h1>

                    <!-- Desktop table (hidden on mobile) -->
                    <div class="products-page__desktop">
                        <table class="table-brut">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th class="products-page__num">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="product in store.products" :key="product.id">
                                <td>
                                    <input type="text" v-model="product.name" class="field-input products-page__input"/>
                                </td>
                                <td>
                                    <input type="text" v-model="product.description" class="field-input products-page__input"/>
                                </td>
                                <td>
                                    <div class="products-page__price-group">
                                        <input type="number" v-model="product.price"
                                               min="0" @keydown="preventNegative"
                                               class="field-input products-page__price-input"/>
                                        <select v-model="product.variable_price"
                                                class="field-select products-page__price-unit">
                                            <option :value="false">€</option>
                                            <option :value="true">€/kg</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="products-page__actions">
                                        <button class="chunk chunk--teal chunk--sm products-page__btn-sm" @click="save(product)">Save</button>
                                        <button type="button" class="icon-btn icon-btn--danger" @click="remove(product)">
                                            <FontAwesomeIcon icon="fa-solid fa-xmark" class="icon-btn__icon" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="products-page__new-row">
                                <td>
                                    <input type="text" placeholder="Name for new product" v-model="newProduct.name"
                                           class="field-input products-page__input"/>
                                </td>
                                <td>
                                    <input type="text" placeholder="Description for new product"
                                           v-model="newProduct.description" class="field-input products-page__input"/>
                                </td>
                                <td>
                                    <div class="products-page__price-group">
                                        <input type="number" v-model="newProduct.price"
                                               placeholder="0.00" min="0" @keydown="preventNegative"
                                               class="field-input products-page__price-input"/>
                                        <select v-model="newProduct.variable_price"
                                                class="field-select products-page__price-unit">
                                            <option :value="false">€</option>
                                            <option :value="true">€/kg</option>
                                        </select>
                                    </div>
                                </td>
                                <td class="products-page__num">
                                    <button class="chunk chunk--teal chunk--sm products-page__btn-sm" @click="saveNew(newProduct)">Add</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile card layout (visible only on mobile) -->
                    <div class="products-page__mobile">
                        <div v-for="product in store.products" :key="product.id" class="products-page__card">
                            <div>
                                <label class="field-label products-page__label-sm">Name</label>
                                <input type="text" v-model="product.name" class="field-input products-page__input-mobile"/>
                            </div>
                            <div>
                                <label class="field-label products-page__label-sm">Description</label>
                                <input type="text" v-model="product.description" class="field-input products-page__input-mobile"/>
                            </div>
                            <div>
                                <label class="field-label products-page__label-sm">Price</label>
                                <div class="products-page__price-group">
                                    <input type="number" v-model="product.price"
                                           min="0" @keydown="preventNegative"
                                           class="field-input products-page__price-input-mobile"/>
                                    <select v-model="product.variable_price"
                                            class="field-select products-page__price-unit-mobile">
                                        <option :value="false">€</option>
                                        <option :value="true">€/kg</option>
                                    </select>
                                </div>
                            </div>
                            <div class="products-page__actions-mobile">
                                <button class="chunk chunk--teal chunk--sm products-page__btn-mobile" @click="save(product)">Save</button>
                                <button type="button" class="icon-btn icon-btn--danger" @click="remove(product)">
                                    <FontAwesomeIcon icon="fa-solid fa-xmark" class="icon-btn__icon" />
                                </button>
                            </div>
                        </div>

                        <!-- New product card (mobile) -->
                        <div class="products-page__card products-page__card--new">
                            <p class="products-page__card-title">New product</p>
                            <div>
                                <label class="field-label products-page__label-sm">Name</label>
                                <input type="text" placeholder="Name for new product" v-model="newProduct.name"
                                       class="field-input products-page__input-mobile"/>
                            </div>
                            <div>
                                <label class="field-label products-page__label-sm">Description</label>
                                <input type="text" placeholder="Description for new product"
                                       v-model="newProduct.description" class="field-input products-page__input-mobile"/>
                            </div>
                            <div>
                                <label class="field-label products-page__label-sm">Price</label>
                                <div class="products-page__price-group">
                                    <input type="number" v-model="newProduct.price"
                                           placeholder="0.00" min="0" @keydown="preventNegative"
                                           class="field-input products-page__price-input-mobile"/>
                                    <select v-model="newProduct.variable_price"
                                            class="field-select products-page__price-unit-mobile">
                                        <option :value="false">€</option>
                                        <option :value="true">€/kg</option>
                                    </select>
                                </div>
                            </div>
                            <div class="products-page__actions-mobile--single">
                                <button class="chunk chunk--teal chunk--sm products-page__btn-mobile" @click="saveNew(newProduct)">Add</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<style scoped>
@import "@css/pages/store/products.css";
</style>