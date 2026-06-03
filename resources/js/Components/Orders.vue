<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import {Link, router} from "@inertiajs/vue3"
import {useHelpers} from "@/Composables/helpers";

const helper = useHelpers();
const toast = useToast();

export default {
    name: "Orders",
    components: {
        FontAwesomeIcon, Link
    },
    data() {
        return {
            request: null,
            showDeliveryModal: false,
            deliveryOrders: [],
            extraItems: [],
            analyzing: false,
            receiptStore: { store_id: null, store_name: null },
            scanStoreId: null,
            scannedPrices: null,
            extraStores: [],
            addingStore: false,
            showCatalogueModal: false,
            newProducts: [],
            priceChanges: [],
        };
    },
    computed: {
        hasPendingOrders() {
            return this.orders.some(o => o.paid_by === null);
        },
        isRunner() {
            const userId = this.$page.props.auth.user.id;
            return this.orders.some(o => o.paid_by === userId && o.delivered_at === null);
        },
        hasRunnerDeparted() {
            return this.orders.some(o => o.departed_at !== null);
        },
      
        isRunnerNotDeparted() {
            const userId = this.$page.props.auth.user.id;
            return this.orders.some(o => o.paid_by === userId && o.departed_at === null && o.delivered_at === null);
        },
        deliveryTotal() {
            const orders = this.deliveryOrders.reduce((sum, o) => sum + (Number(o.total) || 0), 0);
            const extras = this.extraItems.reduce((sum, e) => sum + (Number(e.total) || 0), 0);
            return orders + extras;
        },
        runStoreId() {
            const ids = [...new Set(this.deliveryOrders.map(o => o.product ? o.product.store_id : null).filter(Boolean))];
            return ids.length === 1 ? ids[0] : null;
        },
        targetStoreId() {
            return this.scanStoreId;
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
        statusLabel(order) {
            if (order.delivered_at) return 'Delivered';
            if (order.departed_at) return 'On the way';
            if (order.paid_by) return 'Runner assigned';
            return 'Open';
        },
        statusBadgeClass(order) {
            if (order.delivered_at) return 'badge-success';
            if (order.departed_at) return 'badge-warning';
            if (order.paid_by) return 'badge-info';
            return 'badge-ghost';
        },
        openDeliveryModal() {
            const userId = this.$page.props.auth.user.id;
            this.deliveryOrders = this.orders
                .filter(o => o.paid_by === userId && !o.delivered_at)
                .map(o => ({ ...o, weight: o.weight ?? null, total: Number(o.total), originalTotal: Number(o.total) }));
            this.extraItems = [];
            this.analyzing = false;
            this.receiptStore = { store_id: null, store_name: null };
            this.scannedPrices = null;
            this.scanStoreId = this.runStoreId;
            this.showCatalogueModal = false;
            this.newProducts = [];
            this.priceChanges = [];
            this.showDeliveryModal = true;
        },
        addExtraItem() {
            this.extraItems.push({ label: '', total: null, user_id: null });
        },
        removeExtraItem(index) {
            this.extraItems.splice(index, 1);
        },
        storeById(id) {
            return this.allStores.find(s => s.id === id) || null;
        },
        async addDetectedStore() {
            if (!this.receiptStore.store_name) {
                return;
            }
            this.addingStore = true;
            try {
                const { data } = await axios.post(route('order.receipt.store'), { name: this.receiptStore.store_name });
                this.extraStores.push({ id: data.id, name: data.name, products: [] });
                this.receiptStore.store_id = data.id;
                this.scanStoreId = data.id;
                toast.success(`Store "${data.name}" added.`);
            } catch (error) {
                toast.error('Could not add the store. Please pick one from the list.');
            } finally {
                this.addingStore = false;
            }
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
            return this.extraItems.filter(e => e.label && !this.productExistsInStore(e.label, this.targetStoreId));
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
        // Apply the AI prices, but only to orders of the store this scan is scoped to.
        applyScannedPrices() {
            this.deliveryOrders.forEach(order => { order.total = order.originalTotal; });
            if (!this.scannedPrices) {
                return 0;
            }
            let applied = 0;
            this.scannedPrices.forEach(p => {
                if (p.total === null || p.total === undefined) {
                    return;
                }
                const order = this.deliveryOrders.find(o => o.id === p.order_id);
                if (!order) {
                    return;
                }
                const orderStoreId = order.product ? order.product.store_id : null;
                if (this.scanStoreId && orderStoreId !== this.scanStoreId) {
                    return;
                }
                order.total = Number(p.total);
                applied++;
            });
            return applied;
        },
        updateTotalFromWeight(order) {
            if (order.product.variable_price && order.weight) {
                order.total = order.product.price * parseFloat(order.weight);
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
                const { data } = await axios.post(route('order.receipt.analyze'), formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
                this.receiptStore = data.store || { store_id: null, store_name: null };
                this.scannedPrices = data.prices || [];
                if (this.receiptStore.store_id) {
                    this.scanStoreId = this.receiptStore.store_id;
                }
                const matched = this.applyScannedPrices();
                (data.extras || []).forEach(e => {
                    this.extraItems.push({ label: e.label, total: Number(e.total), user_id: null });
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
        async confirmDelivery() {
            const invalidPrice = (value) => value === null || value === '' || isNaN(Number(value)) || Number(value) < 0;

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
                this.newProducts = newItems.map(e => ({
                    name: e.label,
                    price: Number(e.total),
                    store_id: this.targetStoreId,
                    add: true,
                }));
                this.priceChanges = priceChanges;
                this.showCatalogueModal = true;
                return;
            }

            await this.finalizeDelivery();
        },
        skipCatalogueChanges() {
            this.newProducts.forEach(p => { p.add = false; });
            this.priceChanges.forEach(p => { p.apply = false; });
            this.finalizeDelivery();
        },
        async finalizeDelivery() {
            const invalidPrice = (value) => value === null || value === '' || isNaN(Number(value)) || Number(value) < 0;

            // Best-effort catalogue changes: update changed prices and add chosen new items.
            let failed = 0;

            const toUpdate = this.priceChanges.filter(p => p.apply && p.product_id && !invalidPrice(p.new_price));
            for (const change of toUpdate) {
                try {
                    await axios.patch(route('order.receipt.price'), {
                        product_id: change.product_id,
                        price: Number(change.new_price),
                    });
                } catch (error) {
                    failed++;
                }
            }

            const toAdd = this.newProducts.filter(p => p.add && p.name && p.store_id && !invalidPrice(p.price));
            for (const product of toAdd) {
                try {
                    await axios.post(route('order.receipt.product'), {
                        store_id: product.store_id,
                        name: product.name,
                        price: Number(product.price),
                    });
                } catch (error) {
                    failed++;
                }
            }
            if (failed) {
                toast.error(`${failed} catalogue change(s) could not be saved.`);
            }

            try {
                await axios.patch(route('order.prices'), {
                    store_id: this.targetStoreId,
                    prices: this.deliveryOrders.map(o => ({ order_id: o.id, total: Number(o.total) })),
                    extra_items: this.extraItems.map(e => ({ label: e.label, total: Number(e.total), user_id: e.user_id })),
                });
            } catch (error) {
                toast.error('Could not save the prices. Please try again.');
                return;
            }
            this.showCatalogueModal = false;
            this.showDeliveryModal = false;
            router.patch(route('order.deliver'), {}, {
                only: ['orders', 'totalPrice', 'flash'],
            });
        },
    },
    watch: {
        // Re-scope the scanned prices when the runner changes the ticket's store.
        scanStoreId() {
            if (this.scannedPrices) {
                this.applyScannedPrices();
            }
        },
    },
    props: {
        deliveryMoment: String,
        orders: Array,
        totalPrice: Number,
        companyUsers: Array,
        stores: Array,
    },
}
</script>
<template>
<div>
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h2>Orders for {{ deliveryMoment }}</h2>
                <div class="flex gap-2">
                    <Link :href="route('order.assign-to-me')"
                          v-if="hasPendingOrders"
                          method="post"
                          as="button"
                          type="button"
                          class="btn btn-sm btn-primary"
                          :only="['orders','totalPrice','flash']"
                    >Assign to me
                    </Link>
                    <Link :href="route('order.depart')"
                          v-if="isRunnerNotDeparted"
                          method="patch"
                          as="button"
                          type="button"
                          class="btn btn-sm btn-warning"
                          :only="['orders','totalPrice','flash']"
                    >I'm leaving
                    </Link>
                    <button v-if="isRunner && hasRunnerDeparted"
                            type="button"
                            class="btn btn-sm btn-success"
                            @click="openDeliveryModal"
                    >All delivered
                    </button>
                </div>
            </div>
            <div class="mb-5 flex flex-col gap-2">
                <div v-for="order in orders"
                     class="card card-compact bg-gray-50 shadow-sm" v-if="orders.length">
                    <div class="sm:flex sm:items-start card-body">
                        <div class="text-sm font-medium text-gray-900 flex-1">
                            <span class="text-gray-500 inline-block mr-4">{{ order.store_name }}</span>{{ order.product ? order.product.name : order.label }} <span v-if="order.comment">({{ order.comment }})</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center gap-2 flex-wrap">
                            <span class="badge badge-outline badge-success">{{ formatMoney(order.total) }}</span>
                            <span class="hidden sm:inline" aria-hidden="true">&middot;</span>
                            <span class="badge badge-info">{{ order.user.name }}</span>
                            <span class="hidden sm:inline" aria-hidden="true">&middot;</span>
                            <span :class="statusBadgeClass(order)" class="badge">{{ statusLabel(order) }}</span>
                        </div>
                        <div class="card-actions justify-end w-full">
                            <Link v-if="order.user_id === $page.props.auth.user.id && !order.delivered_at"
                                  :href="route('order.remove-product')"
                                  method="post"
                                  as="button"
                                  type="button"
                                  class="text-error hover:text-red-600"
                                  :data="{ product_id: order.product_id, }"
                                  :only="['orders','flash','selectedRunner','totalPrice']"
                            >
                                <FontAwesomeIcon icon="fas-fa fa-trash"/>
                            </Link>
                        </div>
                    </div>
                </div>
                <div v-else class="card card-compact bg-gray-50 shadow-sm">
                    <div class="card-body">
                        <p class="font-bold">There are no orders yet</p>
                    </div>
                </div>
            </div>

            <h3 class="pl-4"
                v-if="totalPrice !== 0"
            > Total:
                {{ formatMoney(totalPrice.toFixed(2)) }}
            </h3>
        </div>

    </div>

    <Teleport to="body">

        <div v-if="showDeliveryModal && !showCatalogueModal" class="modal modal-open modal-bottom sm:modal-middle">
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

                <div v-if="scannedPrices || extraItems.length" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Store this ticket is for</label>
                    <div class="flex flex-wrap items-center gap-2">
                        <select v-model="scanStoreId" class="select select-bordered select-sm w-full max-w-xs">
                            <option :value="null" disabled>Choose store…</option>
                            <option v-for="store in allStores" :key="store.id" :value="store.id">{{ store.name }}</option>
                        </select>
                        <button v-if="receiptStore.store_name && !detectedStoreName"
                                type="button"
                                class="btn btn-sm btn-outline"
                                :disabled="addingStore"
                                @click="addDetectedStore">
                            <span v-if="addingStore" class="loading loading-spinner loading-xs"></span>
                            + Add "{{ receiptStore.store_name }}"
                        </button>
                    </div>
                    <p v-if="detectedStoreName" class="text-xs text-gray-500 mt-1">
                        Recognised from the ticket: <span class="font-medium">{{ detectedStoreName }}</span>. Only this store's orders get the scanned prices.
                    </p>
                    <p v-else-if="receiptStore.store_name" class="text-xs text-warning mt-1">
                        Ticket store "{{ receiptStore.store_name }}" isn't in your stores yet — add it, or pick one from the list.
                    </p>
                </div>

                <h4 class="font-semibold text-base text-gray-700">Order items</h4>
                <p class="text-xs text-gray-400 mb-2">Scanned items that belong to the order — automatically assigned to the person who ordered them.</p>
                <table class="table w-full mb-5">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Person</th>
                            <th class="text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in deliveryOrders" :key="order.id">
                            <td>
                                <span class="font-medium">{{ order.product ? order.product.name : order.label }}</span>
                                <span v-if="order.comment" class="text-gray-500 text-sm ml-1">({{ order.comment }})</span>
                                <div v-if="order.product && order.product.variable_price" class="flex items-center gap-2 mt-1">
                                    <input type="number" v-model="order.weight" min="0" step="0.01"
                                           class="input input-bordered input-sm w-28" placeholder="0.00"
                                           @input="updateTotalFromWeight(order)"/>
                                    <span class="text-xs text-gray-500">kg</span>
                                </div>
                            </td>
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
                <p class="text-xs text-gray-400 mb-2">Items added during pickup that were not ordered — assign each one to a person.</p>
                <table v-if="extraItems.length" class="table w-full mb-4">
                    <thead>
                        <tr>
                            <th>Description</th>
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
                    <button class="btn" @click="showDeliveryModal = false">Cancel</button>
                    <button class="btn btn-success" @click="confirmDelivery">Confirm</button>
                </div>
            </div>
        </div>

        <div v-if="showCatalogueModal" class="modal modal-open modal-bottom sm:modal-middle">
            <div class="modal-box w-11/12 max-w-3xl">
                <h3 class="text-xl font-bold mb-1">Update the catalogue</h3>
                <p class="text-xs text-gray-500 mb-4">
                    Confirm the changes to your store catalogue before delivering.
                    <span v-if="detectedStoreName">Detected store: <span class="font-medium">{{ detectedStoreName }}</span>.</span>
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
                    <button class="btn" @click="skipCatalogueChanges">Skip</button>
                    <button class="btn btn-success" @click="finalizeDelivery">Confirm changes</button>
                </div>
            </div>
        </div>
    </Teleport>
</div>
</template>

