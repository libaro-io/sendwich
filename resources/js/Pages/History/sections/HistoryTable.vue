<script setup lang="ts">
import axios from "axios";
import {ref, computed, onMounted} from "vue";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {useToast} from "vue-toastification";
import moment from "moment"
import CatalogueUpdate from "@/Components/catalogue-update-component.vue";
import type {Product, User, Store} from '@interfaces/dashboard';

interface NewItem {
    user_id: number | null;
    store_id: number | null;
    product_id: number | null;
    label: string;
    total: number | string | null;
}

interface EditBackup {
    product_id: number | null;
    label: string | null;
    total: number | string | null;
}

type PendingSave =
    | { type: 'edit'; item: any }
    | { type: 'new'; group: any };

const props = defineProps<{
    products: Product[];
    users: User[];
    stores: Store[];
}>();

const toast = useToast();

const orders = ref<any[]>([]);
const editingId = ref<number | null>(null);
const editBackup = ref<EditBackup | null>(null);
const addingDate = ref<string | null>(null);
const newItem = ref<NewItem>({user_id: null, store_id: null, product_id: null, label: '', total: null});
const catalogueProducts = ref<any[]>([]);
const pendingSave = ref<PendingSave | null>(null);

const newItemProducts = computed<Product[]>(() => {
    if (!newItem.value.store_id) {
        return [];
    }
    return props.products.filter(product => product.store_id === newItem.value.store_id);
});

const getData = (): void => {
    axios.post(route('orders.by-date'), {}).then(response => {
        orders.value = response.data;
    }).catch(error => {
        console.log(error);
    });
};

const currentDateTime = (orderByDate: string): string => {
    return moment(orderByDate, "YYYYMMDD").format('dddd - DD/MM/YYYY');
};

const money = (value: number | string | null): string => {
    return new Intl.NumberFormat('nl-BE', {style: 'currency', currency: 'EUR'}).format(Number(value) || 0);
};

const totalOrders = (orderGroup: any[]): number => {
    return orderGroup.reduce((sum, order) => sum + (Number(order.total) || 0), 0);
};

const sortedGroup = (orderGroup: any[]): any[] => {
    // Order items (with a product) first, then the extra items.
    return [...orderGroup].sort((a, b) => (a.product ? 0 : 1) - (b.product ? 0 : 1));
};

const updateRunner = (orderGroup: any[], runnerId: string | number | null, event: Event): void => {
    const runnerName = runnerId
        ? (props.users.find(user => user.id === parseInt(String(runnerId)))?.name || 'deze persoon')
        : 'geen runner';

    const message = runnerId
        ? `Weet je zeker dat je de runner wilt aanpassen naar ${runnerName}?`
        : 'Weet je zeker dat je de runner wilt verwijderen?';

    if (!confirm(message)) {
        if (event) {
            (event.target as HTMLSelectElement).value = orderGroup[0] ? orderGroup[0].paid_by : '';
        }
        return;
    }

    const orderIds = orderGroup.map(order => order.id);
    const parsedRunnerId = runnerId ? parseInt(String(runnerId)) : null;
    axios.post(route('history.update-runner'), {
        order_ids: orderIds,
        runner_id: parsedRunnerId,
    }).then(() => {
        // Update in place so the order stays in its group — only the runner label changes.
        const runner = parsedRunnerId ? (props.users.find(user => user.id === parsedRunnerId) || {id: parsedRunnerId}) : null;
        orderGroup.forEach(order => {
            order.paid_by = parsedRunnerId;
            order.deliverer = runner;
        });
        toast.success('Runner updated');
    }).catch(error => {
        toast.error('Failed to update runner');
        console.log(error);
    });
};

const startEdit = (item: any): void => {
    editingId.value = item.id;
    item.editProductId = item.product ? item.product.id : null; // null = custom item
    item.editStoreId = item.product ? item.product.store_id : (item.store_id ?? null);
    editBackup.value = {
        product_id: item.product ? item.product.id : null,
        label: item.label,
        total: item.total,
    };
};

const cancelEdit = (item: any): void => {
    if (editBackup.value) {
        item.label = editBackup.value.label;
        item.total = editBackup.value.total;
    }
    editingId.value = null;
    editBackup.value = null;
};

const onEditProductChange = (item: any): void => {
    if (item.editProductId == null) {
        // Switched to a custom item — prefill the name from the current product, if any.
        if (!item.label && item.product) {
            item.label = item.product.name;
        }
        return;
    }
    const product = props.products.find(product => product.id === item.editProductId);
    if (product && product.price != null) {
        item.total = product.price;
    }
};

const productExistsInStore = (label: string, storeId: number): boolean => {
    const needle = (label || '').trim().toLowerCase();
    return props.products.some(product => product.store_id === storeId && (product.name || '').trim().toLowerCase() === needle);
};

const filteredProducts = (item: any): Product[] => {
    if (!item.editStoreId) {
        return props.products;
    }
    return props.products.filter(product => product.store_id === item.editStoreId);
};

const onStoreChange = (item: any): void => {
    const available = filteredProducts(item);
    if (item.editProductId != null && !available.some(product => product.id === item.editProductId)) {
        // The selected product isn't in the chosen store — switch to its first product, or custom.
        const first = available[0];
        item.editProductId = first ? first.id : null;
        if (first && first.price != null) {
            item.total = first.price;
        }
    }
};

const performSaveEdit = (item: any, addToCatalogue: boolean, catalogueRow: any): void => {
    axios.patch(route('order.edit'), {
        order_id: item.id,
        product_id: item.editProductId ?? null,
        label: addToCatalogue && catalogueRow ? catalogueRow.name : item.label,
        store_id: addToCatalogue && catalogueRow ? catalogueRow.store_id : (item.editStoreId ?? null),
        total: parseFloat(item.total),
        add_to_catalogue: addToCatalogue,
        catalogue_price: addToCatalogue && catalogueRow ? Number(catalogueRow.price) : null,
    }).then(() => {
        editingId.value = null;
        editBackup.value = null;
        getData();
        toast.success('Updated');
    }).catch(() => {
        toast.error('Failed to update');
    });
};

const saveEdit = (item: any): void => {
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
    if (!productId && item.label && item.editStoreId && !productExistsInStore(item.label, item.editStoreId)) {
        catalogueProducts.value = [{add: true, name: item.label, store_id: item.editStoreId, price: parseFloat(item.total)}];
        pendingSave.value = {type: 'edit', item: item};
        return;
    }

    performSaveEdit(item, false, null);
};

const deleteItem = (item: any): void => {
    if (!window.confirm('Delete this item?')) {
        return;
    }
    axios.delete(route('order.delete', item.id)).then(() => {
        getData();
        toast.success('Deleted');
    }).catch(() => {
        toast.error('Failed to delete');
    });
};

const startAdd = (group: any): void => {
    addingDate.value = group.date;
    newItem.value = {user_id: null, store_id: null, product_id: null, label: '', total: null};
};

const cancelAdd = (): void => {
    addingDate.value = null;
};

const onNewStoreChange = (): void => {
    // The product list depends on the store — drop the selection back to a custom item.
    newItem.value.product_id = null;
};

const onNewProductChange = (): void => {
    if (newItem.value.product_id == null) {
        return;
    }
    const product = props.products.find(product => product.id === newItem.value.product_id);
    if (product) {
        newItem.value.label = product.name;
        if (product.price != null) {
            newItem.value.total = product.price;
        }
    }
};

const performSaveNew = (group: any, addToCatalogue: boolean, catalogueRow: any): void => {
    axios.post(route('order.custom.add'), {
        date: group.date,
        user_id: newItem.value.user_id,
        product_id: newItem.value.product_id ?? null,
        store_id: addToCatalogue && catalogueRow ? catalogueRow.store_id : newItem.value.store_id,
        label: addToCatalogue && catalogueRow ? catalogueRow.name : newItem.value.label,
        total: parseFloat(String(newItem.value.total)),
        paid_by: group.data[0] ? group.data[0].paid_by : null,
        add_to_catalogue: addToCatalogue,
        catalogue_price: addToCatalogue && catalogueRow ? Number(catalogueRow.price) : null,
    }).then(() => {
        addingDate.value = null;
        getData();
        toast.success('Item added');
    }).catch(() => {
        toast.error('Failed to add item');
    });
};

const saveNew = (group: any): void => {
    if (!newItem.value.user_id) {
        toast.error('Please choose a person');
        return;
    }
    if (newItem.value.total === null || newItem.value.total === '' || isNaN(parseFloat(String(newItem.value.total))) || parseFloat(String(newItem.value.total)) < 0) {
        toast.error('Please enter a valid price');
        return;
    }

    // An existing product was picked from the store — add it directly, no catalogue pop-up.
    if (newItem.value.product_id != null) {
        performSaveNew(group, false, null);
        return;
    }

    if (!newItem.value.label) {
        toast.error('Please enter a name or pick a product');
        return;
    }
    // A custom item that doesn't exist in the chosen store yet → ask via the catalogue pop-up.
    if (newItem.value.store_id && !productExistsInStore(newItem.value.label, newItem.value.store_id)) {
        catalogueProducts.value = [{add: true, name: newItem.value.label, store_id: newItem.value.store_id, price: parseFloat(String(newItem.value.total))}];
        pendingSave.value = {type: 'new', group: group};
        return;
    }

    performSaveNew(group, false, null);
};

const resolveCatalogue = (addToCatalogue: boolean): void => {
    const catalogueRow = catalogueProducts.value[0] ?? null;
    const pending = pendingSave.value;
    pendingSave.value = null;
    catalogueProducts.value = [];

    if (!pending) {
        return;
    }
    if (pending.type === 'edit') {
        performSaveEdit(pending.item, addToCatalogue, catalogueRow);
        return;
    }
    performSaveNew(pending.group, addToCatalogue, catalogueRow);
};

const onCatalogueSkip = (): void => {
    resolveCatalogue(false);
};

const onCatalogueConfirm = (): void => {
    const row = catalogueProducts.value[0];
    const valid = row && row.add && row.name && row.store_id && !isNaN(Number(row.price)) && Number(row.price) >= 0;
    resolveCatalogue(Boolean(valid));
};

onMounted(() => {
    getData();
});
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
                                        {{ user.id === $page.props.auth.user?.id ? 'You (' + user.name + ')' : user.name }}
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
