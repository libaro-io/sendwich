<script setup lang="ts">
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useHelpers} from "../Composables/helpers";
import type {NewProduct, PriceChange, Store} from "@interfaces/catalogue-update";

defineProps<{
    newProducts: NewProduct[];
    priceChanges: PriceChange[];
    stores: Store[];
    submitting?: boolean;
}>();

defineEmits(['skip', 'confirm']);

const {formatMoney} = useHelpers();
</script>
<template>
    <section class="component-catalogue-update-component">
        <Teleport to="body">
            <div class="catalogue-update">
                <div class="catalogue-update__backdrop"></div>
                <div class="catalogue-update__box">
                    <div class="catalogue-update__head">
                        <h3 class="catalogue-update__title">Update the catalogue</h3>
                        <p class="catalogue-update__sub">
                            Confirm the changes to your store catalogue before delivering.
                        </p>
                    </div>

                    <div class="catalogue-update__body">
                        <template v-if="newProducts.length">
                            <h4 class="catalogue-update__section-title">Add to store</h4>
                            <p class="catalogue-update__section-sub">New items from the receipt that aren't in your stores yet.</p>

                            <!-- Desktop Table -->
                            <div class="catalogue-update__desktop">
                                <table class="table-brut catalogue-update__table">
                                    <thead>
                                    <tr>
                                        <th>Add</th>
                                        <th>Name</th>
                                        <th>Store</th>
                                        <th class="catalogue-update__cell-right">Price</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(product, index) in newProducts" :key="index">
                                        <td>
                                            <input type="checkbox" v-model="product.add" class="field-checkbox"/>
                                        </td>
                                        <td>
                                            <input type="text" v-model="product.name" :disabled="!product.add"
                                                   class="field-input" placeholder="Product name"/>
                                        </td>
                                        <td>
                                            <select v-model="product.store_id" :disabled="!product.add" class="field-select">
                                                <option :value="null" disabled>Choose store…</option>
                                                <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                                            </select>
                                        </td>
                                        <td class="catalogue-update__cell-right">
                                            <input type="number" v-model="product.price" :disabled="!product.add" min="0" step="0.01"
                                                   class="field-input catalogue-update__price-input" placeholder="0.00"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Cards -->
                            <div class="catalogue-update__mobile">
                                <div v-for="(product, index) in newProducts" :key="index" class="catalogue-update__card">
                                    <label class="catalogue-update__check">
                                        <input type="checkbox" v-model="product.add" class="field-checkbox"/>
                                        <span class="catalogue-update__check-label" :class="{'catalogue-update__check-label--off': !product.add}">Add item</span>
                                    </label>
                                    <input type="text" v-model="product.name" :disabled="!product.add"
                                           class="field-input" placeholder="Product name"/>
                                    <div class="catalogue-update__card-row">
                                        <select v-model="product.store_id" :disabled="!product.add" class="field-select catalogue-update__grow">
                                            <option :value="null" disabled>Store…</option>
                                            <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                                        </select>
                                        <input type="number" v-model="product.price" :disabled="!product.add" min="0" step="0.01"
                                               class="field-input catalogue-update__price-input" placeholder="0.00"/>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-if="priceChanges.length">
                            <h4 class="catalogue-update__section-title">Update prices</h4>
                            <p class="catalogue-update__section-sub">Products whose price you changed — update the catalogue price for next time.</p>

                            <!-- Desktop Table -->
                            <div class="catalogue-update__desktop">
                                <table class="table-brut catalogue-update__table">
                                    <thead>
                                    <tr>
                                        <th>Update</th>
                                        <th>Product</th>
                                        <th class="catalogue-update__cell-right">Current</th>
                                        <th class="catalogue-update__cell-right">New price</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(change, index) in priceChanges" :key="index">
                                        <td>
                                            <input type="checkbox" v-model="change.apply" class="field-checkbox"/>
                                        </td>
                                        <td>{{ change.name }}</td>
                                        <td class="catalogue-update__cell-right catalogue-update__old-price">{{ formatMoney(change.current_price) }}</td>
                                        <td class="catalogue-update__cell-right">
                                            <input type="number" v-model="change.new_price" :disabled="!change.apply" min="0" step="0.01"
                                                   class="field-input catalogue-update__price-input" placeholder="0.00"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Cards -->
                            <div class="catalogue-update__mobile">
                                <div v-for="(change, index) in priceChanges" :key="index" class="catalogue-update__card">
                                    <label class="catalogue-update__check">
                                        <input type="checkbox" v-model="change.apply" class="field-checkbox"/>
                                        <span class="catalogue-update__check-label">{{ change.name }}</span>
                                    </label>
                                    <div class="catalogue-update__card-row catalogue-update__card-row--between">
                                    <span class="catalogue-update__current">
                                        Current: <span class="catalogue-update__old-price">{{ formatMoney(change.current_price) }}</span>
                                    </span>
                                        <input type="number" v-model="change.new_price" :disabled="!change.apply" min="0" step="0.01"
                                               class="field-input catalogue-update__price-input" placeholder="0.00"/>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="catalogue-update__actions">
                        <button class="chunk chunk--ghost" :disabled="submitting" @click="$emit('skip')">Skip</button>
                        <button class="chunk chunk--teal catalogue-update__confirm" :disabled="submitting" @click="$emit('confirm')">
                            <FontAwesomeIcon v-if="submitting" icon="fa-solid fa-spinner" spin/>
                            Confirm changes
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </section>
</template>
<style scoped>
@import "@css/components/catalogue-update-component.css";
</style>
