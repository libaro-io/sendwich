<script>
import {useHelpers} from "@/Composables/helpers";

const helper = useHelpers();

export default {
    name: "CatalogueUpdate",
    props: {
        newProducts: Array,
        priceChanges: Array,
        stores: Array,
        submitting: Boolean,
    },
    emits: ['skip', 'confirm'],
    methods: {
        formatMoney: helper.formatMoney,
    },
}
</script>
<template>
    <Teleport to="body">
        <div class="modal modal-open modal-bottom sm:modal-middle">
            <div class="modal-box w-full sm:max-w-3xl p-4 sm:p-6 max-h-[90vh] flex flex-col">
                <div class="shrink-0">
                    <h3 class="text-xl font-bold mb-1">Update the catalogue</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Confirm the changes to your store catalogue before delivering.
                    </p>
                </div>

                <div class="flex-grow overflow-y-auto pr-1">
                    <template v-if="newProducts.length">
                        <h4 class="font-semibold text-base text-gray-700">Add to store</h4>
                        <p class="text-xs text-gray-400 mb-2">New items from the receipt that aren't in your stores yet.</p>
                        
                        <!-- Desktop Table -->
                        <div class="hidden sm:block">
                            <table class="table w-full mb-5">
                                <thead>
                                    <tr>
                                        <th>Add</th>
                                        <th>Name</th>
                                        <th>Store</th>
                                        <th class="text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, index) in newProducts" :key="index">
                                        <td>
                                            <input type="checkbox" v-model="product.add" class="checkbox checkbox-sm checkbox-primary"/>
                                        </td>
                                        <td>
                                            <input type="text" v-model="product.name" :disabled="!product.add"
                                                   class="input input-bordered input-sm w-full" placeholder="Product name"/>
                                        </td>
                                        <td>
                                            <select v-model="product.store_id" :disabled="!product.add"
                                                    class="select select-bordered select-sm w-full">
                                                <option :value="null" disabled>Choose store…</option>
                                                <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                                            </select>
                                        </td>
                                        <td class="text-right">
                                            <input type="number" v-model="product.price" :disabled="!product.add" min="0" step="0.01"
                                                   class="input input-bordered input-sm w-28 text-right" placeholder="0.00"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards (Matching project style) -->
                        <div class="sm:hidden flex flex-col gap-2 mb-6">
                            <div v-for="(product, index) in newProducts" :key="index" 
                                 class="card card-compact bg-gray-50 shadow-sm border border-gray-100">
                                <div class="card-body">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" v-model="product.add" class="checkbox checkbox-sm checkbox-primary"/>
                                            <span class="text-sm font-bold" :class="!product.add ? 'text-gray-400' : 'text-gray-700'">Add item</span>
                                        </label>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <input type="text" v-model="product.name" :disabled="!product.add"
                                               class="input input-bordered input-sm w-full" placeholder="Product name"/>
                                        <div class="flex gap-2">
                                            <select v-model="product.store_id" :disabled="!product.add"
                                                    class="select select-bordered select-sm flex-1">
                                                <option :value="null" disabled>Store…</option>
                                                <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                                            </select>
                                            <input type="number" v-model="product.price" :disabled="!product.add" min="0" step="0.01"
                                                   class="input input-bordered input-sm w-24 text-right" placeholder="0.00"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-if="priceChanges.length">
                        <h4 class="font-semibold text-base text-gray-700">Update prices</h4>
                        <p class="text-xs text-gray-400 mb-2">Products whose price you changed — update the catalogue price for next time.</p>
                        
                        <!-- Desktop Table -->
                        <div class="hidden sm:block">
                            <table class="table w-full mb-5">
                                <thead>
                                    <tr>
                                        <th>Update</th>
                                        <th>Product</th>
                                        <th class="text-right">Current</th>
                                        <th class="text-right">New price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(change, index) in priceChanges" :key="index">
                                        <td>
                                            <input type="checkbox" v-model="change.apply" class="checkbox checkbox-sm checkbox-primary"/>
                                        </td>
                                        <td>{{ change.name }}</td>
                                        <td class="text-right text-gray-500 line-through">{{ formatMoney(change.current_price) }}</td>
                                        <td class="text-right">
                                            <input type="number" v-model="change.new_price" :disabled="!change.apply" min="0" step="0.01"
                                                   class="input input-bordered input-sm w-28 text-right" placeholder="0.00"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="sm:hidden flex flex-col gap-2 mb-6">
                            <div v-for="(change, index) in priceChanges" :key="index" 
                                 class="card card-compact bg-gray-50 shadow-sm border border-gray-100">
                                <div class="card-body">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" v-model="change.apply" class="checkbox checkbox-sm checkbox-primary"/>
                                            <span class="text-sm font-bold text-gray-700">{{ change.name }}</span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="text-xs text-gray-500">
                                            Current: <span class="line-through">{{ formatMoney(change.current_price) }}</span>
                                        </div>
                                        <input type="number" v-model="change.new_price" :disabled="!change.apply" min="0" step="0.01"
                                               class="input input-bordered input-sm w-24 text-right" placeholder="0.00"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="modal-action shrink-0 mt-6">
                    <button class="btn btn-ghost" :disabled="submitting" @click="$emit('skip')">Skip</button>
                    <button class="btn btn-success flex-1 sm:flex-initial" :disabled="submitting" @click="$emit('confirm')">
                        <span v-if="submitting" class="loading loading-spinner loading-xs"></span>
                        Confirm changes
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
