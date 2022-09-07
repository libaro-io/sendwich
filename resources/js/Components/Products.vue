<template>
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Bon'app</h3>
            <div class="mt-5">
                <div v-for="product in products"
                     class="rounded-md mb-1 bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                            <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center">
                                <div><span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">€ {{
                                        product.price
                                    }}</span></div>
                                <span class="hidden sm:mx-2 sm:inline"
                                      aria-hidden="true"> &middot; </span>
                                <div class="mt-1 sm:mt-0">{{ deliveryMoment }}</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p
                            v-for="option in product.options"
                            :key="option.id"
                        >
                            <Checkbox
                                @update:checked="(e) => addOrRemoveOption(e, option)"
                                :checked="option.is_enabled_by_default"
                            />
                            {{ option.name }} €{{ option.price }}
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6 sm:flex-shrink-0">
                        <button
                            class="btn btn-sm btn-primary"
                            @click="addProduct(product)"
                        >
                            Bestel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Checkbox from '@/Components/Checkbox.vue';
import { useToast } from "vue-toastification";

const toast = useToast();

export default {
    name: "Products",
    components: {
        Checkbox,
    },
    props: {
        deliveryMoment: String,
        products: Array,
    },
    data() {
        return {
            selectedOptions: [],
        };
    },
    methods: {
        addProduct (product) {
            axios.post('/api/order/add-product', {
                product_id: product.id,
                options: this.getSelectedOptionIdsForProduct(product.id),
            }).then(response => {
                toast.success(response.data.message);
                this.emitter.emit('updateOrders');
            }).catch(error => {
                console.error(error);
            });
        },

        getSelectedOptionIdsForProduct(productId) {
            return this.selectedOptions.filter(o => o.product_id === productId).map(o => o.id);
        },

        addOrRemoveOption(shouldAdd, option) {
            if (shouldAdd) {
                this.addOption(option);
            } else {
                this.removeOption(option);
            }
        },

        addOption(option) {
            this.selectedOptions.push(option);
        },

        removeOption(option) {
            const index = this.selectedOptions.indexOf(option);

            this.selectedOptions.splice(index, 1);
        },
    }
}
</script>
