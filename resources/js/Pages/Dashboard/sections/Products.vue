<script>
import Checkbox from '@/Components/ui/Checkbox.vue';
import {useToast} from "vue-toastification";
import ProductCard from "@/Pages/Dashboard/sections/ProductCard.vue";
import Modal from "@/Components/ui/Modal.vue";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {Link} from "@inertiajs/vue3";

const toast = useToast();

export default {
    name: "Products",
    components: {
        ProductCard,
        Checkbox,
        Modal,
        FontAwesomeIcon,
        Link,
    },
    props: {
        products: Array,
        filters : Object,
        productCount : Number,
        orderingBlocked: Boolean,
    },
    mounted() {
        this.searchedProducts = this.products
    },
    computed: {
        filteredProducts() {
            if (!this.search) {
                return this.products;
            }
            const searchTerm = this.search.toLowerCase();
            return this.products.filter(product =>
                product.name.toLowerCase().includes(searchTerm) ||
                (product.description && product.description.toLowerCase().includes(searchTerm))
            );
        }
    },
    data() {
        return {
            selectedOptions: [],
            search: this.filters.search,
            searchedProducts: null,
            toggle: false,
            selectedProduct: null,
            selectedDescription: null,
        };
    },
    methods: {
        addProduct(product, comment) {
            var app = this;
            var options = app.getSelectedOptionIdsForProduct(product)
            axios.post(route('order.check-new-store'), {
                product_id: product.id,
                options: options,
            }).then(function(response){
                app.confirmOrder(product, options, comment);
            }).catch(function (error){
                app.showConfirmationModal(product, options, comment);
            });
        },

        confirmOrder(product, options, comment) {
            this.toggle = false;
            this.$inertia.post(route('order.add-product'), {
                product_id: product.id,
                options: options,
                comment: comment || null,
            },{
                only:['orders','totalPrice','flash','selectedRunner']
            });
        },

        showConfirmationModal(product, options, comment){
            this.selectedProduct = product;
            this.selectedOptions = options;
            this.selectedDescription = comment;
            this.toggle = true;
        },

        getSelectedOptionIdsForProduct(product) {
            return product.options.filter(option => option.selected === true).map(option => option.id);
        },
    }
}
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