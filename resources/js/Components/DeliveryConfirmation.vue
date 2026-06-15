<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import {router} from "@inertiajs/vue3";
import {useHelpers} from "@/Composables/helpers";
import CatalogueUpdate from "@/Components/CatalogueUpdate.vue";

const helper = useHelpers();
const toast = useToast();

const invalidPrice = (value) => value === null || value === '' || isNaN(Number(value)) || Number(value) < 0;

export default {
    name: "DeliveryConfirmation",
    components: {FontAwesomeIcon, CatalogueUpdate},
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
            const seen = new Set();
            const result = [];
            for (const e of this.extraItems) {
                if (!e.label || this.productExistsInStore(e.label, e.store_id)) {
                    continue;
                }
                const key = e.label.trim().toLowerCase() + '|' + e.store_id;
                if (seen.has(key)) {
                    continue;
                }
                seen.add(key);
                result.push(e);
            }
            return result;
        },
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
                const recognisedStoreId = (this.receiptStore.store_id && this.allStores.some(s => s.id === this.receiptStore.store_id))
                    ? this.receiptStore.store_id
                    : null;
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
                only: ['orders', 'totalPrice', 'flash', 'users'],
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
            <div class="modal-box w-full sm:max-w-4xl rounded-t-3xl sm:rounded-2xl p-4 sm:p-6 max-h-[90vh] flex flex-col">
                <h3 class="text-xl font-bold mb-4 shrink-0">Confirm delivery</h3>

                <div class="flex-grow overflow-y-auto pr-1">
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
                    
                    <!-- Desktop Table -->
                    <div class="hidden sm:block">
                        <table class="table w-full mb-5">
                            <thead>
                                <tr>
                                    <th class="bg-gray-50">Item</th>
                                    <th class="bg-gray-50">Store</th>
                                    <th class="bg-gray-50">Person</th>
                                    <th class="bg-gray-50 text-right">Price</th>
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
                    </div>

                    <!-- Mobile List -->
                    <div class="sm:hidden space-y-3 mb-6">
                        <div v-for="order in deliveryOrders" :key="order.id" 
                             class="bg-gray-50 p-3 rounded-xl border border-gray-100 flex flex-col gap-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-semibold text-gray-900 leading-tight">
                                        {{ order.product ? order.product.name : order.label }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ order.store_name }} &middot; {{ order.user.name }}
                                    </div>
                                </div>
                                <div class="relative w-28">
                                    <span class="absolute left-2 top-1.5 text-xs text-gray-400">€</span>
                                    <input type="number" v-model="order.total" min="0" step="0.01"
                                           class="input input-bordered input-sm w-full pl-5 text-right font-medium" placeholder="0.00"/>
                                </div>
                            </div>
                            <div v-if="order.comment" class="text-xs text-gray-500 italic">"{{ order.comment }}"</div>
                            <div v-if="order.product && order.product.variable_price" class="text-[10px] text-amber-600 font-medium uppercase tracking-wider">
                                Indicative price — enter actual paid
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-base text-gray-700">Extra items</h4>
                        <button type="button" class="btn btn-xs btn-ghost text-primary" @click="addExtraItem">+ Add item</button>
                    </div>
                    <p class="text-xs text-gray-400 mb-2">Items added during pickup that were not ordered.</p>
                    
                    <!-- Desktop Table -->
                    <div class="hidden sm:block">
                        <table v-if="extraItems.length" class="table w-full mb-4">
                            <thead>
                                <tr>
                                    <th class="bg-gray-50">Description</th>
                                    <th class="bg-gray-50">Store</th>
                                    <th class="bg-gray-50">Assign to</th>
                                    <th class="bg-gray-50 text-right">Price</th>
                                    <th class="bg-gray-50"></th>
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
                    </div>

                    <!-- Mobile List -->
                    <div class="sm:hidden space-y-4 mb-4">
                        <div v-for="(extra, index) in extraItems" :key="index" 
                             class="bg-gray-50 p-3 rounded-xl border border-gray-100 flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-700 uppercase tracking-tight">Extra Item #{{ index + 1 }}</span>
                                <button type="button" class="btn btn-ghost btn-xs text-error" @click="removeExtraItem(index)">
                                    Remove
                                </button>
                            </div>
                            <input type="text" v-model="extra.label"
                                   class="input input-bordered input-sm w-full" placeholder="What is it?"/>
                            <div class="grid grid-cols-2 gap-2">
                                <select v-model="extra.store_id" class="select select-bordered select-sm w-full">
                                    <option :value="null" disabled>Store…</option>
                                    <option v-for="store in allStores" :key="store.id" :value="store.id">{{ store.name }}</option>
                                </select>
                                <select v-model="extra.user_id" class="select select-bordered select-sm w-full">
                                    <option :value="null" disabled>For who?</option>
                                    <option v-for="person in companyUsers" :key="person.id" :value="person.id">
                                        {{ person.id === $page.props.auth.user.id ? 'You' : person.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="relative">
                                <span class="absolute left-2 top-1.5 text-xs text-gray-400">€</span>
                                <input type="number" v-model="extra.total" min="0" step="0.01"
                                       class="input input-bordered input-sm w-full pl-5 text-right font-medium" placeholder="0.00"/>
                            </div>
                        </div>
                        <p v-if="!extraItems.length" class="text-xs text-gray-400 italic mb-4">No extra items.</p>
                    </div>
                </div>

                <div class="flex justify-between items-center font-bold border-t pt-4 mt-2 shrink-0">
                    <span class="text-lg">Total</span>
                    <span class="text-xl text-primary">{{ formatMoney(deliveryTotal) }}</span>
                </div>
                <div class="modal-action mt-6 shrink-0">
                    <button class="btn btn-ghost" :disabled="submitting" @click="$emit('close')">Cancel</button>
                    <button class="btn btn-success flex-1" :disabled="submitting" @click="confirm">
                        <span v-if="submitting" class="loading loading-spinner loading-xs"></span>
                        Confirm Delivery
                    </button>
                </div>
            </div>
        </div>
    </Teleport>

    <CatalogueUpdate v-if="showCatalogue"
                     :new-products="newProducts"
                     :price-changes="priceChanges"
                     :stores="allStores"
                     :submitting="submitting"
                     @skip="skipCatalogueChanges"
                     @confirm="finalize"/>
</template>
