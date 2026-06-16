<script>
import axios from "axios";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import moment from "moment"
import CatalogueUpdate from "@/Components/catalogue-update-component.vue";

const toast = useToast();

export default {
    name: "History",
    components: {
        FontAwesomeIcon, CatalogueUpdate,
    },
    mounted() {
        this.getData()
    },
    props: {
        products: Array,
        users: Array,
        stores: Array,
    },
    data() {
        return {
            orders: [],
            editingId: null,
            editBackup: null,
            addingDate: null,
            newItem: {user_id: null, store_id: null, product_id: null, label: '', total: null},
            catalogueProducts: [],
            pendingSave: null,
        };
    },
    computed: {
        newItemProducts() {
            if (!this.newItem.store_id) {
                return [];
            }
            return this.products.filter(p => p.store_id === this.newItem.store_id);
        },
    },
    methods: {
        getData() {
            axios.post(route('orders.by-date'), {}).then(response => {
                this.orders = response.data;
            }).catch(error => {
                console.log(error);
            });
        },
        currentDateTime(orderByDate) {
            return moment(orderByDate, "YYYYMMDD").format('dddd - DD/MM/YYYY');
        },
        money(value) {
            return new Intl.NumberFormat('nl-BE', {style: 'currency', currency: 'EUR'}).format(value || 0);
        },
        totalOrders(orders) {
            return orders.reduce((sum, order) => sum + (Number(order.total) || 0), 0);
        },
        sortedGroup(orderGroup) {
            // Order items (with a product) first, then the extra items.
            return [...orderGroup].sort((a, b) => (a.product ? 0 : 1) - (b.product ? 0 : 1));
        },
        updateRunner(orderGroup, runnerId, event) {
            const runnerName = runnerId
                ? (this.users.find(u => u.id === parseInt(runnerId))?.name || 'deze persoon')
                : 'geen runner';

            const message = runnerId
                ? `Weet je zeker dat je de runner wilt aanpassen naar ${runnerName}?`
                : 'Weet je zeker dat je de runner wilt verwijderen?';

            if (!confirm(message)) {
                if (event) {
                    event.target.value = orderGroup[0] ? orderGroup[0].paid_by : '';
                }
                return;
            }

            const orderIds = orderGroup.map(order => order.id);
            const parsedRunnerId = runnerId ? parseInt(runnerId) : null;
            axios.post(route('history.update-runner'), {
                order_ids: orderIds,
                runner_id: parsedRunnerId,
            }).then(() => {
                // Update in place so the order stays in its group — only the runner label changes.
                const runner = parsedRunnerId ? (this.users.find(u => u.id === parsedRunnerId) || {id: parsedRunnerId}) : null;
                orderGroup.forEach(order => {
                    order.paid_by = parsedRunnerId;
                    order.deliverer = runner;
                });
                toast.success('Runner updated');
            }).catch(error => {
                toast.error('Failed to update runner');
                console.log(error);
            });
        },
        startEdit(item) {
            this.editingId = item.id;
            item.editProductId = item.product ? item.product.id : null; // null = custom item
            item.editStoreId = item.product ? item.product.store_id : (item.store_id ?? null);
            this.editBackup = {
                product_id: item.product ? item.product.id : null,
                label: item.label,
                total: item.total,
            };
        },
        cancelEdit(item) {
            if (this.editBackup) {
                item.label = this.editBackup.label;
                item.total = this.editBackup.total;
            }
            this.editingId = null;
            this.editBackup = null;
        },
        onEditProductChange(item) {
            if (item.editProductId == null) {
                // Switched to a custom item — prefill the name from the current product, if any.
                if (!item.label && item.product) {
                    item.label = item.product.name;
                }
                return;
            }
            const product = this.products.find(p => p.id === item.editProductId);
            if (product && product.price != null) {
                item.total = product.price;
            }
        },
        productExistsInStore(label, storeId) {
            const needle = (label || '').trim().toLowerCase();
            return this.products.some(p => p.store_id === storeId && (p.name || '').trim().toLowerCase() === needle);
        },
        filteredProducts(item) {
            if (!item.editStoreId) {
                return this.products;
            }
            return this.products.filter(p => p.store_id === item.editStoreId);
        },
        onStoreChange(item) {
            const available = this.filteredProducts(item);
            if (item.editProductId != null && !available.some(p => p.id === item.editProductId)) {
                // The selected product isn't in the chosen store — switch to its first product, or custom.
                const first = available[0];
                item.editProductId = first ? first.id : null;
                if (first && first.price != null) {
                    item.total = first.price;
                }
            }
        },
        saveEdit(item) {
            if (item.total === null || item.total === '' || isNaN(parseFloat(item.total)) || parseFloat(item.total) < 0) {
                toast.error('Please enter a valid price');
                return;
            }
            const productId = item.editProductId ?? null;
            if (!productId && !item.label) {
                toast.error('Please enter a name or pick a product');
                return;
            }

            // A custom item that doesn't exist in the chosen store yet → ask via the catalogue pop-up.
            if (!productId && item.label && item.editStoreId && !this.productExistsInStore(item.label, item.editStoreId)) {
                this.catalogueProducts = [{add: true, name: item.label, store_id: item.editStoreId, price: parseFloat(item.total)}];
                this.pendingSave = {type: 'edit', item: item};
                return;
            }

            this.performSaveEdit(item, false, null);
        },
        performSaveEdit(item, addToCatalogue, catalogueRow) {
            axios.patch(route('order.edit'), {
                order_id: item.id,
                product_id: item.editProductId ?? null,
                label: addToCatalogue && catalogueRow ? catalogueRow.name : item.label,
                store_id: addToCatalogue && catalogueRow ? catalogueRow.store_id : (item.editStoreId ?? null),
                total: parseFloat(item.total),
                add_to_catalogue: addToCatalogue,
                catalogue_price: addToCatalogue && catalogueRow ? Number(catalogueRow.price) : null,
            }).then(() => {
                this.editingId = null;
                this.editBackup = null;
                this.getData();
                toast.success('Updated');
            }).catch(() => {
                toast.error('Failed to update');
            });
        },
        deleteItem(item) {
            if (!window.confirm('Delete this item?')) {
                return;
            }
            axios.delete(route('order.delete', item.id)).then(() => {
                this.getData();
                toast.success('Deleted');
            }).catch(() => {
                toast.error('Failed to delete');
            });
        },
        startAdd(group) {
            this.addingDate = group.date;
            this.newItem = {user_id: null, store_id: null, product_id: null, label: '', total: null};
        },
        cancelAdd() {
            this.addingDate = null;
        },
        onNewStoreChange() {
            // The product list depends on the store — drop the selection back to a custom item.
            this.newItem.product_id = null;
        },
        onNewProductChange() {
            if (this.newItem.product_id == null) {
                return;
            }
            const product = this.products.find(p => p.id === this.newItem.product_id);
            if (product) {
                this.newItem.label = product.name;
                if (product.price != null) {
                    this.newItem.total = product.price;
                }
            }
        },
        saveNew(group) {
            if (!this.newItem.user_id) {
                toast.error('Please choose a person');
                return;
            }
            if (this.newItem.total === null || this.newItem.total === '' || isNaN(parseFloat(this.newItem.total)) || parseFloat(this.newItem.total) < 0) {
                toast.error('Please enter a valid price');
                return;
            }

            // An existing product was picked from the store — add it directly, no catalogue pop-up.
            if (this.newItem.product_id != null) {
                this.performSaveNew(group, false, null);
                return;
            }

            if (!this.newItem.label) {
                toast.error('Please enter a name or pick a product');
                return;
            }
            // A custom item that doesn't exist in the chosen store yet → ask via the catalogue pop-up.
            if (this.newItem.store_id && !this.productExistsInStore(this.newItem.label, this.newItem.store_id)) {
                this.catalogueProducts = [{add: true, name: this.newItem.label, store_id: this.newItem.store_id, price: parseFloat(this.newItem.total)}];
                this.pendingSave = {type: 'new', group: group};
                return;
            }

            this.performSaveNew(group, false, null);
        },
        performSaveNew(group, addToCatalogue, catalogueRow) {
            axios.post(route('order.custom.add'), {
                date: group.date,
                user_id: this.newItem.user_id,
                product_id: this.newItem.product_id ?? null,
                store_id: addToCatalogue && catalogueRow ? catalogueRow.store_id : this.newItem.store_id,
                label: addToCatalogue && catalogueRow ? catalogueRow.name : this.newItem.label,
                total: parseFloat(this.newItem.total),
                paid_by: group.data[0] ? group.data[0].paid_by : null,
                add_to_catalogue: addToCatalogue,
                catalogue_price: addToCatalogue && catalogueRow ? Number(catalogueRow.price) : null,
            }).then(() => {
                this.addingDate = null;
                this.getData();
                toast.success('Item added');
            }).catch(() => {
                toast.error('Failed to add item');
            });
        },
        onCatalogueSkip() {
            this.resolveCatalogue(false);
        },
        onCatalogueConfirm() {
            const row = this.catalogueProducts[0];
            const valid = row && row.add && row.name && row.store_id && !isNaN(Number(row.price)) && Number(row.price) >= 0;
            this.resolveCatalogue(Boolean(valid));
        },
        resolveCatalogue(addToCatalogue) {
            const catalogueRow = this.catalogueProducts[0] ?? null;
            const pending = this.pendingSave;
            this.pendingSave = null;
            this.catalogueProducts = [];

            if (!pending) {
                return;
            }
            if (pending.type === 'edit') {
                this.performSaveEdit(pending.item, addToCatalogue, catalogueRow);
                return;
            }
            this.performSaveNew(pending.group, addToCatalogue, catalogueRow);
        },
    }
}
</script>
<template>
    <div class="panel history">
        <h1 class="history__title">History</h1>
        <article v-for="group in orders" :key="group.date" class="history__group">
            <h2 class="history__date">{{ currentDateTime(group.date) }}</h2>
            <div class="history__scroll">
                <table class="table-brut history__table">
                    <thead>
                    <tr>
                        <th>Ordered by</th>
                        <th>Store</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th class="history__num">Total</th>
                        <th class="history__num">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in sortedGroup(group.data)" :key="item.id">
                        <td class="history__cell-user">{{ item.user.name }}</td>

                        <td class="history__muted history__cell-store">
                            <select v-if="editingId === item.id" v-model="item.editStoreId" @change="onStoreChange(item)"
                                    class="field-select history__inline-select history__store-select">
                                <option :value="null">—</option>
                                <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                            </select>
                            <template v-else>{{ item.product?.store?.name || item.store?.name || '—' }}</template>
                        </td>

                        <td>
                            <template v-if="editingId === item.id">
                                <select v-model="item.editProductId" @change="onEditProductChange(item)"
                                        class="field-select history__inline-select">
                                    <option :value="null">— Custom item —</option>
                                    <option v-for="product in filteredProducts(item)" :key="product.id" :value="product.id">{{ product.name }}</option>
                                </select>
                                <input v-if="item.editProductId == null" type="text" v-model="item.label"
                                       class="field-input history__inline-input history__inline-spaced" placeholder="Item name"/>
                            </template>
                            <template v-else>
                                <span class="history__product-name">{{ item.product ? item.product.name : item.label }}</span>
                                <span v-if="!item.product" class="tag tag--ink history__extra-tag">extra</span>
                                <template v-if="item.comment">
                                    <br>
                                    <span class="history__comment">{{ item.comment }}</span>
                                </template>
                            </template>
                        </td>

                        <td>
                            <template v-if="item.quantity > 0">{{ money(item.total / item.quantity) }}</template>
                            <span v-else class="history__muted">—</span>
                        </td>

                        <td class="history__cell-qty">{{ item.quantity }}</td>

                        <td class="history__num">
                            <input v-if="editingId === item.id" type="number" v-model="item.total" min="0" step="0.01"
                                   class="field-input history__price-input" placeholder="0.00"/>
                            <span v-else class="history__total-value">{{ money(item.total) }}</span>
                        </td>

                        <td class="history__num">
                            <div class="history__actions">
                                <template v-if="editingId === item.id">
                                    <button class="chunk chunk--teal chunk--sm" @click="saveEdit(item)">Save</button>
                                    <button class="chunk chunk--ghost chunk--sm" @click="cancelEdit(item)">Cancel</button>
                                </template>
                                <template v-else>
                                    <button class="icon-btn" title="Edit" @click="startEdit(item)">
                                        <FontAwesomeIcon icon="fa-solid fa-pen"/>
                                    </button>
                                    <button class="icon-btn icon-btn--danger" title="Delete" @click="deleteItem(item)">
                                        <FontAwesomeIcon icon="fa-solid fa-trash"/>
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="addingDate === group.date">
                        <td>
                            <select v-model="newItem.user_id" class="field-select history__inline-select">
                                <option :value="null" disabled>Choose person…</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </td>
                        <td class="history__cell-store">
                            <select v-model="newItem.store_id" @change="onNewStoreChange" class="field-select history__inline-select history__store-select">
                                <option :value="null">—</option>
                                <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                            </select>
                        </td>
                        <td>
                            <template v-if="newItem.store_id">
                                <select v-model="newItem.product_id" @change="onNewProductChange" class="field-select history__inline-select">
                                    <option :value="null">— Custom item —</option>
                                    <option v-for="product in newItemProducts" :key="product.id" :value="product.id">{{ product.name }}</option>
                                </select>
                                <input v-if="newItem.product_id == null" type="text" v-model="newItem.label"
                                       class="field-input history__inline-input history__inline-spaced" placeholder="Item name"/>
                            </template>
                            <input v-else type="text" v-model="newItem.label" class="field-input history__inline-input" placeholder="Item name"/>
                        </td>
                        <td><span class="history__muted">—</span></td>
                        <td>1</td>
                        <td class="history__num">
                            <input type="number" v-model="newItem.total" min="0" step="0.01" class="field-input history__price-input" placeholder="0.00"/>
                        </td>
                        <td class="history__num">
                            <div class="history__actions">
                                <button class="chunk chunk--teal chunk--sm" @click="saveNew(group)">Save</button>
                                <button class="chunk chunk--ghost chunk--sm" @click="cancelAdd">Cancel</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="history__runner-row">
                        <td colspan="5">
                            <div class="history__runner-wrap">
                                <span class="history__runner-label">Runner:</span>
                                <select class="field-select history__runner-select"
                                        :value="group.data[0] ? group.data[0].paid_by : null"
                                        @change="updateRunner(group.data, $event.target.value, $event)">
                                    <option :value="null">No runner</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.id === $page.props.auth.user.id ? 'You (' + user.name + ')' : user.name }}
                                    </option>
                                </select>
                            </div>
                        </td>
                        <td class="history__num">
                            Total: <span class="history__total-value">{{ money(totalOrders(group.data)) }}</span>
                        </td>
                        <td class="history__num">
                            <button v-if="addingDate !== group.date" class="chunk chunk--ghost chunk--sm" @click="startAdd(group)">+ Add item</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </article>

        <CatalogueUpdate v-if="pendingSave"
                         :new-products="catalogueProducts"
                         :price-changes="[]"
                         :stores="stores"
                         :submitting="false"
                         @skip="onCatalogueSkip"
                         @confirm="onCatalogueConfirm"/>
    </div>
</template>

<style scoped>
@import "@css/pages/history/sections/history-table.css";
</style>
