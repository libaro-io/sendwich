<script setup lang="ts">
import {ref, computed} from "vue";
import {router, Link} from "@inertiajs/vue3";
import axios from "axios";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import Modal from "@/Components/ui/modal-component.vue";
import ProductCard from "@/Pages/Dashboard/sections/ProductCard.vue";
import type {Product, DashboardFilters} from '@interfaces/dashboard';

const props = defineProps<{
    products: Product[];
    filters: DashboardFilters;
    productCount: number;
    orderingBlocked?: boolean;
}>();

defineEmits<{
    unsetStore: [];
}>();

const selectedOptions = ref<number[]>([]);
const search = ref(props.filters.search ?? '');
const toggle = ref(false);
const selectedProduct = ref<Product | null>(null);
const selectedDescription = ref<string | null>(null);

const filteredProducts = computed(() => {
    if (!search.value) {
        return props.products;
    }
    const searchTerm = search.value.toLowerCase();
    return props.products.filter(product =>
        product.name.toLowerCase().includes(searchTerm) ||
        (product.description && product.description.toLowerCase().includes(searchTerm))
    );
});

const getSelectedOptionIdsForProduct = (product: Product): number[] => {
    return product.options.filter(option => option.selected === true).map(option => option.id);
};

const confirmOrder = (product: Product | null, options: number[], comment: string | null): void => {
    if (!product) {
        return;
    }
    toggle.value = false;
    router.post(route('order.add-product'), {
        product_id: product.id,
        options: options,
        comment: comment || null,
    }, {
        only: ['orders', 'totalPrice', 'flash', 'selectedRunner'],
    });
};

const showConfirmationModal = (product: Product, options: number[], comment: string | null): void => {
    selectedProduct.value = product;
    selectedOptions.value = options;
    selectedDescription.value = comment;
    toggle.value = true;
};

const addProduct = (product: Product, comment: string): void => {
    const options = getSelectedOptionIdsForProduct(product);
    axios.post(route('order.check-new-store'), {
        product_id: product.id,
        options: options,
    }).then(() => {
        confirmOrder(product, options, comment);
    }).catch(() => {
        showConfirmationModal(product, options, comment);
    });
};
</script>
<template>
    <div class="panel">
        <h2 class="panel-title">Menu</h2>
        <div v-if="productCount">
           <div class="products__search-row">
               <button @click="$emit('unsetStore')" class="products__back-btn">
                   <FontAwesomeIcon icon="fa-solid fa-arrow-left" class="products__back-icon" />
               </button>
               <input type="text" placeholder="Search a product..." class="field-input" v-model="search"/>
           </div>
        </div>
        <div v-if="orderingBlocked" class="callout callout--warning products__notice">
            The runner is on the way — new orders are no longer possible.
        </div>
        <div v-else-if="filteredProducts.length" class="products__list">
            <div v-for="(product, index) in filteredProducts"
                 :key="index"
                 class="panel panel--flat">
                <product-card :product="product" @ordered="addProduct"></product-card>
            </div>
        </div>
        <Link v-else class="callout callout--info products__empty" :href="route('store.index')">
            <div>
                <h3 class="products__empty-title">Nothing to feed yourself</h3>
                <div class="products__empty-desc">Add a store and products to get yourself started</div>
            </div>
        </Link>

        <!--    confirmation modal when another store selected-->
        <Modal :open="toggle" title="You added a product from a different store!" @close="toggle = false">
            <p class="products__modal-text">Are you sure? The runner will have to visit more than one store.</p>
            <template #actions>
                <button class="chunk chunk--teal" @click="confirmOrder(selectedProduct, selectedOptions, selectedDescription)">Confirm</button>
                <button class="chunk chunk--ghost" @click="toggle = false">Nah, drop it!</button>
            </template>
        </Modal>
    </div>
</template>

<style scoped>
@import "@css/pages/dashboard/sections/products.css";
</style>
