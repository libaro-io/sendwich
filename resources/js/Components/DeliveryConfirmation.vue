<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import {router} from "@inertiajs/vue3";
import {useHelpers} from "@/Composables/helpers";

const helper = useHelpers();
const toast = useToast();

const invalidPrice = (value) => value === null || value === '' || isNaN(Number(value)) || Number(value) < 0;

export default {
    name: "DeliveryConfirmation",
    components: {FontAwesomeIcon},
    props: {
        orders: Array,
        companyUsers: Array,
        stores: Array,
    },
    emits: ['close'],
    data() {
        return {
            deliveryOrders: [],
            extraItems: [],
            analyzing: false,
            receiptStore: {store_id: null, store_name: null},
            extraStores: [],
            addingStore: false,
            submitting: false,
            showCatalogue: false,
            newProducts: [],
            priceChanges: [],
        };
    },
    created() {
        const userId = this.$page.props.auth.user.id;
        this.deliveryOrders = this.orders
            .filter(o => o.paid_by === userId && !o.delivered_at)
            .map(o => ({...o, total: Number(o.total), originalTotal: Number(o.total)}));
    },
    computed: {
        deliveryTotal() {
            const orders = this.deliveryOrders.reduce((sum, o) => sum + (Number(o.total) || 0), 0);
            const extras = this.extraItems.reduce((sum, e) => sum + (Number(e.total) || 0), 0);
            return orders + extras;
        },
        runStoreId() {
            const ids = [...new Set(this.deliveryOrders.map(o => o.product ? o.product.store_id : null).filter(Boolean))];
            return ids.length === 1 ? ids[0] : null;
        },
        allStores() {
            return [...(this.stores || []), ...this.extraStores];
        },
        detectedStoreName() {
            const store = this.allStores.find(s => s.id === this.receiptStore.store_id);
            return store ? store.name : null;
        },
    },
    methods: {
        formatMoney: helper.formatMoney,
        addExtraItem() {
            this.extraItems.push({label: '', total: null, user_id: null, store_id: this.runStoreId});
        },
        removeExtraItem(index) {
            this.extraItems.splice(index, 1);
        },
        storeById(id) {
            return this.allStores.find(s => s.id === id) || null;
        },
        productExistsInStore(label, storeId) {
            const store = this.storeById(storeId);
            if (!store || !store.products) {
                return false;
            }
            const needle = (label || '').trim().toLowerCase();
            return store.products.some(p => (p.name || '').trim().toLowerCase() === needle);
        },
        computeNewItems() {
            return this.extraItems.filter(e => e.label && !this.productExistsInStore(e.label, e.store_id));
        },
        // Fixed-price ordered products whose price the runner changed → suggest updating the catalogue price.
        computePriceChanges() {
            const cents = (value) => Math.round(Number(value) * 100);
            const seen = new Set();
            const changes = [];
            for (const order of this.deliveryOrders) {
                if (!order.product || order.product.variable_price) {
                    continue;
                }
                if (cents(order.total) === cents(order.originalTotal) || seen.has(order.product.id)) {
                    continue;
                }
                seen.add(order.product.id);
                const unit = order.quantity ? Number(order.total) / order.quantity : Number(order.total);
                changes.push({
                    product_id:    order.product.id,
                    name:          order.product.name,
                    store_id:      order.product.store_id,
                    current_price: Number(order.product.price),
                    new_price:     Math.round(unit * 100) / 100,
                    apply:         true,
                });
            }
            return changes;
        },
        async addDetectedStore() {
            if (!this.receiptStore.store_name) {
                return;
            }
            this.addingStore = true;
            try {
                const {data} = await axios.post(route('order.receipt.store'), {name: this.receiptStore.store_name});
                this.extraStores.push({id: data.id, name: data.name, products: []});
                this.receiptStore.store_id = data.id;
                // Tag the just-scanned (yet unassigned) extras with the new store.
                this.extraItems.forEach(e => { if (e.store_id === null) { e.store_id = data.id; } });
                toast.success(`Store "${data.name}" added.`);
            } catch (error) {
                toast.error('Could not add the store. Please pick one from the list.');
            } finally {
                this.addingStore = false;
            }
        },
        async analyzeReceipt(event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }
            this.analyzing = true;
            const formData = new FormData();
            formData.append('image', file);
            try {
                const {data} = await axios.post(route('order.receipt.analyze'), formData, {
                    headers: {'Content-Type': 'multipart/form-data'},
                });
                this.receiptStore = data.store || {store_id: null, store_name: null};
                // Only trust the detected store if it matches one of our stores.
                const recognisedStoreId = (this.receiptStore.store_id && this.allStores.some(s => s.id === this.receiptStore.store_id))
                    ? this.receiptStore.store_id
                    : null;
                // Apply prices additively — only to orders of this ticket's recognised store, so a
                // second ticket (other store) never overwrites the first ticket's prices.
                let matched = 0;
                (data.prices || []).forEach(p => {
                    if (p.total === null || p.total === undefined) {
                        return;
                    }
                    const order = this.deliveryOrders.find(o => o.id === p.order_id);
                    if (!order || !order.product || recognisedStoreId === null || order.product.store_id !== recognisedStoreId) {
                        return;
                    }
                    order.total = Number(p.total);
                    matched++;
                });
                (data.extras || []).forEach(e => {
                    this.extraItems.push({label: e.label, total: Number(e.total), user_id: null, store_id: recognisedStoreId});
                });
                const extraNote = (data.extras && data.extras.length)
                    ? `, ${data.extras.length} extra item(s) found — assign them to a person`
                    : '';
                toast.success(`Ticket scanned — ${matched} price(s) filled in${extraNote}. Please review before confirming.`);
            } catch (error) {
                toast.error(error.response?.data?.message || 'Could not analyse the ticket. Please enter the prices manually.');
            } finally {
                this.analyzing = false;
                event.target.value = '';
            }
        },
        confirm() {
            if (this.submitting) {
                return;
            }
            const invalidOrders = this.deliveryOrders.filter(o => invalidPrice(o.total));
            if (invalidOrders.length) {
                toast.error(`Please fill in a valid price for: ${invalidOrders.map(o => o.product ? o.product.name : o.label).join(', ')}`);
                return;
            }
            const invalidExtras = this.extraItems.filter(e => !e.label || !e.user_id || invalidPrice(e.total));
            if (invalidExtras.length) {
                toast.error('Each extra item needs a description, a price and a person to assign it to.');
                return;
            }

            const newItems = this.computeNewItems();
            const priceChanges = this.computePriceChanges();
            if (newItems.length || priceChanges.length) {
                this.newProducts = newItems.map(e => ({name: e.label, price: Number(e.total), store_id: e.store_id, add: true}));
                this.priceChanges = priceChanges;
                this.showCatalogue = true;
                return;
            }

            this.finalize();
        },
        skipCatalogueChanges() {
            this.newProducts.forEach(p => { p.add = false; });
            this.priceChanges.forEach(p => { p.apply = false; });
            this.finalize();
        },
        finalize() {
            if (this.submitting) {
                return;
            }
            this.submitting = true;

            router.post(route('order.confirm-delivery'), {
                prices: this.deliveryOrders.map(o => ({order_id: o.id, total: Number(o.total)})),
                extra_items: this.extraItems.map(e => ({label: e.label, total: Number(e.total), user_id: e.user_id, store_id: e.store_id})),
                new_products: this.newProducts
                    .filter(p => p.add && p.name && p.store_id && !invalidPrice(p.price))
                    .map(p => ({name: p.name, price: Number(p.price), store_id: p.store_id})),
                price_updates: this.priceChanges
                    .filter(p => p.apply && p.product_id && !invalidPrice(p.new_price))
                    .map(p => ({product_id: p.product_id, price: Number(p.new_price)})),
            }, {
                only: ['orders', 'totalPrice', 'flash'],
                preserveScroll: true,
                onSuccess: () => { this.$emit('close'); },
                onError: () => { toast.error('Could not confirm the delivery. Please try again.'); },
                onFinish: () => { this.submitting = false; },
            });
        },
    },
}
</script>
<template>
    <Teleport to="body">
        <div v-if="!showCatalogue" class="modal modal-open modal-bottom sm:modal-middle">
            <div class="modal-box w-11/12 max-w-4xl">
                <h3 class="text-xl font-bold mb-4">Confirm delivery</h3>

                <div class="form-control mb-4">
                    <label class="label py-1">
                        <span class="label-text">Add a photo of the receipt (optional) — the prices are read automatically</span>
                    </label>
                    <input type="file" accept="image/*" capture="environment"
                           class="file-input file-input-bordered file-input-sm w-full"
                           :disabled="analyzing"
                           @change="analyzeReceipt"/>
                    <span v-if="analyzing" class="text-xs text-gray-500 mt-2 flex items-center gap-2">
                        <span class="loading loading-spinner loading-xs"></span>
                        Scanning ticket with AI…
                    </span>
                </div>

                <div v-if="receiptStore.store_name" class="mb-4 text-xs">
                    <p v-if="detectedStoreName" class="text-gray-500">
                        Last ticket recognised as <span class="font-medium">{{ detectedStoreName }}</span> — its prices were filled in for that store's orders. You can scan more tickets for other stores.
                    </p>
                    <div v-else class="flex flex-wrap items-center gap-2">
                        <span class="text-warning">Ticket store "{{ receiptStore.store_name }}" isn't in your stores yet.</span>
                        <button type="button" class="btn btn-xs btn-outline" :disabled="addingStore" @click="addDetectedStore">
                            <span v-if="addingStore" class="loading loading-spinner loading-xs"></span>
                            + Add "{{ receiptStore.store_name }}"
                        </button>
                    </div>
                </div>

                <h4 class="font-semibold text-base text-gray-700">Order items</h4>
                <p class="text-xs text-gray-400 mb-2">Scanned items that belong to the order — automatically assigned to the person who ordered them.</p>
                <table class="table w-full mb-5">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Store</th>
                            <th>Person</th>
                            <th class="text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in deliveryOrders" :key="order.id">
                            <td>
                                <span class="font-medium">{{ order.product ? order.product.name : order.label }}</span>
                                <span v-if="order.comment" class="text-gray-500 text-sm ml-1">({{ order.comment }})</span>
                                <div v-if="order.product && order.product.variable_price" class="text-xs text-amber-600 mt-1">
                                    Indicative price — enter the amount actually paid
                                </div>
                            </td>
                            <td class="text-sm text-gray-500">{{ order.store_name }}</td>
                            <td class="text-sm">{{ order.user.name }}</td>
                            <td class="text-right">
                                <input type="number" v-model="order.total" min="0" step="0.01"
                                       class="input input-bordered input-sm w-28 text-right" placeholder="0.00"/>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex items-center justify-between">
                    <h4 class="font-semibold text-base text-gray-700">Extra items</h4>
                    <button type="button" class="btn btn-xs btn-ghost" @click="addExtraItem">+ Add item</button>
                </div>
                <p class="text-xs text-gray-400 mb-2">Items added during pickup that were not ordered — assign each one to a person and store.</p>
                <table v-if="extraItems.length" class="table w-full mb-4">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Store</th>
                            <th>Assign to</th>
                            <th class="text-right">Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(extra, index) in extraItems" :key="index">
                            <td>
                                <input type="text" v-model="extra.label"
                                       class="input input-bordered input-sm w-full" placeholder="Item name"/>
                            </td>
                            <td>
                                <select v-model="extra.store_id" class="select select-bordered select-sm w-full">
                                    <option :value="null" disabled>Choose store…</option>
                                    <option v-for="store in allStores" :key="store.id" :value="store.id">{{ store.name }}</option>
                                </select>
                            </td>
                            <td>
                                <select v-model="extra.user_id" class="select select-bordered select-sm w-full">
                                    <option :value="null" disabled>Choose person…</option>
                                    <option v-for="person in companyUsers" :key="person.id" :value="person.id">
                                        {{ person.id === $page.props.auth.user.id ? 'You (' + person.name + ')' : person.name }}
                                    </option>
                                </select>
                            </td>
                            <td class="text-right">
                                <input type="number" v-model="extra.total" min="0" step="0.01"
                                       class="input input-bordered input-sm w-28 text-right" placeholder="0.00"/>
                            </td>
                            <td class="text-right">
                                <button type="button" class="text-error hover:text-red-600" @click="removeExtraItem(index)">
                                    <FontAwesomeIcon icon="fas-fa fa-trash"/>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-else class="text-xs text-gray-400 italic mb-4">No extra items.</p>

                <div class="flex justify-end items-center gap-2 font-bold border-t pt-3 mb-1">
                    <span>Total:</span>
                    <span>{{ formatMoney(deliveryTotal) }}</span>
                </div>
                <div class="modal-action">
                    <button class="btn" :disabled="submitting" @click="$emit('close')">Cancel</button>
                    <button class="btn btn-success" :disabled="submitting" @click="confirm">
                        <span v-if="submitting" class="loading loading-spinner loading-xs"></span>
                        Confirm
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showCatalogue" class="modal modal-open modal-bottom sm:modal-middle">
            <div class="modal-box w-11/12 max-w-3xl">
                <h3 class="text-xl font-bold mb-1">Update the catalogue</h3>
                <p class="text-xs text-gray-500 mb-4">
                    Confirm the changes to your store catalogue before delivering.
                </p>

                <template v-if="newProducts.length">
                    <h4 class="font-semibold text-base text-gray-700">Add to store</h4>
                    <p class="text-xs text-gray-400 mb-2">New items from the receipt that aren't in your stores yet.</p>
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
                                    <input type="checkbox" v-model="product.add" class="checkbox checkbox-sm"/>
                                </td>
                                <td>
                                    <input type="text" v-model="product.name" :disabled="!product.add"
                                           class="input input-bordered input-sm w-full" placeholder="Product name"/>
                                </td>
                                <td>
                                    <select v-model="product.store_id" :disabled="!product.add"
                                            class="select select-bordered select-sm w-full">
                                        <option :value="null" disabled>Choose store…</option>
                                        <option v-for="store in allStores" :key="store.id" :value="store.id">{{ store.name }}</option>
                                    </select>
                                </td>
                                <td class="text-right">
                                    <input type="number" v-model="product.price" :disabled="!product.add" min="0" step="0.01"
                                           class="input input-bordered input-sm w-28 text-right" placeholder="0.00"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </template>

                <template v-if="priceChanges.length">
                    <h4 class="font-semibold text-base text-gray-700">Update prices</h4>
                    <p class="text-xs text-gray-400 mb-2">Products whose price you changed — update the catalogue price for next time.</p>
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
                                    <input type="checkbox" v-model="change.apply" class="checkbox checkbox-sm"/>
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
                </template>

                <div class="modal-action">
                    <button class="btn" :disabled="submitting" @click="skipCatalogueChanges">Skip</button>
                    <button class="btn btn-success" :disabled="submitting" @click="finalize">
                        <span v-if="submitting" class="loading loading-spinner loading-xs"></span>
                        Confirm changes
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
