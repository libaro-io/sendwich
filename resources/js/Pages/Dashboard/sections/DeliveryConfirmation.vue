<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import {router} from "@inertiajs/vue3";
import {useHelpers} from "../../../Composables/helpers";
import CatalogueUpdate from "@/Components/catalogue-update-component.vue";

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
        <div v-if="!showCatalogue" class="delivery-confirm">
            <div class="delivery-confirm__backdrop" @click="$emit('close')"></div>
            <div class="delivery-confirm__box">
                <h3 class="delivery-confirm__title">Confirm delivery</h3>

                <div class="delivery-confirm__body">
                    <div class="delivery-confirm__upload">
                        <label class="chunk chunk--sun delivery-confirm__scan" :class="{'delivery-confirm__scan--busy': analyzing}">
                            <FontAwesomeIcon :icon="analyzing ? 'fa-solid fa-spinner' : 'fa-solid fa-receipt'" :spin="analyzing"/>
                            <span>{{ analyzing ? 'Scanning ticket with AI…' : 'Scan receipt with AI' }}</span>
                            <input type="file" accept="image/*" capture="environment"
                                   class="delivery-confirm__file"
                                   :disabled="analyzing"
                                   @change="analyzeReceipt"/>
                        </label>
                        <p class="delivery-confirm__scan-hint">Optional — add a photo of the receipt and the prices are filled in automatically.</p>
                    </div>

                    <div v-if="receiptStore.store_name" class="delivery-confirm__receipt-note">
                        <p v-if="detectedStoreName" class="delivery-confirm__hint">
                            Last ticket recognised as <span class="delivery-confirm__hint-strong">{{ detectedStoreName }}</span> — its prices were filled in for that store's orders. You can scan more tickets for other stores.
                        </p>
                        <div v-else class="delivery-confirm__store-add">
                            <span class="callout callout--warning">Ticket store "{{ receiptStore.store_name }}" isn't in your stores yet.</span>
                            <button type="button" class="chunk chunk--ghost chunk--sm" :disabled="addingStore" @click="addDetectedStore">
                                <FontAwesomeIcon v-if="addingStore" icon="fa-solid fa-spinner" spin/>
                                + Add "{{ receiptStore.store_name }}"
                            </button>
                        </div>
                    </div>

                    <h4 class="delivery-confirm__section-title">Order items</h4>
                    <p class="delivery-confirm__section-sub">Scanned items that belong to the order — automatically assigned to the person who ordered them.</p>

                    <!-- Desktop Table -->
                    <div class="delivery-confirm__desktop">
                        <table class="table-brut delivery-confirm__table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Store</th>
                                    <th>Person</th>
                                    <th class="delivery-confirm__cell-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in deliveryOrders" :key="order.id">
                                    <td>
                                        <span class="delivery-confirm__item-name">{{ order.product ? order.product.name : order.label }}</span>
                                        <span v-if="order.comment" class="delivery-confirm__item-comment">({{ order.comment }})</span>
                                        <div v-if="order.product && order.product.variable_price" class="delivery-confirm__indicative">
                                            Indicative price — enter the amount actually paid
                                        </div>
                                    </td>
                                    <td class="delivery-confirm__muted">{{ order.store_name }}</td>
                                    <td class="delivery-confirm__muted">{{ order.user.name }}</td>
                                    <td class="delivery-confirm__cell-right">
                                        <input type="number" v-model="order.total" min="0" step="0.01"
                                               class="field-input delivery-confirm__price-input" placeholder="0.00"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile List -->
                    <div class="delivery-confirm__mobile">
                        <div v-for="order in deliveryOrders" :key="order.id" class="delivery-confirm__mobile-card">
                            <div class="delivery-confirm__mobile-head">
                                <div>
                                    <div class="delivery-confirm__item-name">
                                        {{ order.product ? order.product.name : order.label }}
                                    </div>
                                    <div class="delivery-confirm__mobile-meta">
                                        {{ order.store_name }} &middot; {{ order.user.name }}
                                    </div>
                                </div>
                                <div class="delivery-confirm__price-field">
                                    <span class="delivery-confirm__price-symbol">€</span>
                                    <input type="number" v-model="order.total" min="0" step="0.01"
                                           class="field-input delivery-confirm__price-input delivery-confirm__price-input--padded" placeholder="0.00"/>
                                </div>
                            </div>
                            <div v-if="order.comment" class="delivery-confirm__mobile-comment">"{{ order.comment }}"</div>
                            <div v-if="order.product && order.product.variable_price" class="delivery-confirm__indicative">
                                Indicative price — enter actual paid
                            </div>
                        </div>
                    </div>

                    <div class="delivery-confirm__section-head">
                        <h4 class="delivery-confirm__section-title">Extra items</h4>
                        <button type="button" class="chunk chunk--ghost chunk--sm" @click="addExtraItem">+ Add item</button>
                    </div>
                    <p class="delivery-confirm__section-sub">Items added during pickup that were not ordered.</p>

                    <!-- Desktop Table -->
                    <div class="delivery-confirm__desktop">
                        <table v-if="extraItems.length" class="table-brut delivery-confirm__table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Store</th>
                                    <th>Assign to</th>
                                    <th class="delivery-confirm__cell-right">Price</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(extra, index) in extraItems" :key="index">
                                    <td>
                                        <input type="text" v-model="extra.label"
                                               class="field-input" placeholder="Item name"/>
                                    </td>
                                    <td>
                                        <select v-model="extra.store_id" class="field-select">
                                            <option :value="null" disabled>Choose store…</option>
                                            <option v-for="store in allStores" :key="store.id" :value="store.id">{{ store.name }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select v-model="extra.user_id" class="field-select">
                                            <option :value="null" disabled>Choose person…</option>
                                            <option v-for="person in companyUsers" :key="person.id" :value="person.id">
                                                {{ person.id === $page.props.auth.user.id ? 'You (' + person.name + ')' : person.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="delivery-confirm__cell-right">
                                        <input type="number" v-model="extra.total" min="0" step="0.01"
                                               class="field-input delivery-confirm__price-input" placeholder="0.00"/>
                                    </td>
                                    <td class="delivery-confirm__cell-right">
                                        <button type="button" class="icon-btn icon-btn--danger" @click="removeExtraItem(index)">
                                            <FontAwesomeIcon icon="fa-solid fa-trash"/>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile List -->
                    <div class="delivery-confirm__mobile">
                        <div v-for="(extra, index) in extraItems" :key="index" class="delivery-confirm__mobile-card">
                            <div class="delivery-confirm__mobile-head">
                                <span class="delivery-confirm__extra-label">Extra Item #{{ index + 1 }}</span>
                                <button type="button" class="chunk chunk--ghost chunk--sm" @click="removeExtraItem(index)">
                                    Remove
                                </button>
                            </div>
                            <input type="text" v-model="extra.label"
                                   class="field-input" placeholder="What is it?"/>
                            <div class="delivery-confirm__mobile-grid">
                                <select v-model="extra.store_id" class="field-select">
                                    <option :value="null" disabled>Store…</option>
                                    <option v-for="store in allStores" :key="store.id" :value="store.id">{{ store.name }}</option>
                                </select>
                                <select v-model="extra.user_id" class="field-select">
                                    <option :value="null" disabled>For who?</option>
                                    <option v-for="person in companyUsers" :key="person.id" :value="person.id">
                                        {{ person.id === $page.props.auth.user.id ? 'You' : person.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="delivery-confirm__price-field">
                                <span class="delivery-confirm__price-symbol">€</span>
                                <input type="number" v-model="extra.total" min="0" step="0.01"
                                       class="field-input delivery-confirm__price-input--padded" placeholder="0.00"/>
                            </div>
                        </div>
                        <p v-if="!extraItems.length" class="delivery-confirm__empty">No extra items.</p>
                    </div>
                </div>

                <div class="delivery-confirm__total">
                    <span class="delivery-confirm__total-label">Total</span>
                    <span class="delivery-confirm__total-value">{{ formatMoney(deliveryTotal) }}</span>
                </div>
                <div class="delivery-confirm__actions">
                    <button class="chunk chunk--ghost" :disabled="submitting" @click="$emit('close')">Cancel</button>
                    <button class="chunk chunk--teal delivery-confirm__confirm" :disabled="submitting" @click="confirm">
                        <FontAwesomeIcon v-if="submitting" icon="fa-solid fa-spinner" spin/>
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

<style scoped>
@import "@css/pages/dashboard/sections/delivery-confirmation.css";
</style>
