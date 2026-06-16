<script>
import {useHelpers} from "../../../Composables/helpers";
import Modal from "@/Components/ui/modal-component.vue";

const helper = useHelpers();
export default {
    name: "productCard",
    components: {
        Modal,
    },
    mounted() {
        this.product.options.forEach(( option ) => option['selected'] = false)
    },
    props: {
        product : Object,
    },
    data() {
        return {
            comment: '',
            showModal: false,
        };
    },
    computed:{
        options(){
            return this.product.options;
        },
    },
    methods:{
        formatMoney: helper.formatMoney,
        toggleOption(option){
            option.selected = !option.selected;
        },
        order(){
            this.$emit('ordered', this.product, this.comment);
            this.comment = '';
            this.showModal = false;
        },
    },
}
</script>
<template>
<div class="product-card">
    <div class="product-card__info">
        <div class="product-card__name">{{ product.name }}</div>
        <div class="product-card__per-kg" v-if="product.variable_price">
            <span class="product-card__per-kg-label">Indicative price</span>
        </div>
    </div>
    <div>
        <button
            type="button"
            @click="showModal = true"
            class="chunk chunk--teal chunk--sm product-card__btn">
            Order
            <span class="tag tag--sun tag--bold">
                {{ product.variable_price ? '≈ ' : '' }}{{ formatMoney(product.price) }}
            </span>
        </button>

        <Modal :open="showModal" :title="product.name" @close="showModal = false">
            <div class="product-card__price-row">
                <span class="tag tag--sun tag--semibold">
                    Price: {{ product.variable_price ? '≈ ' + formatMoney(product.price) : formatMoney(product.price) }}
                </span>
            </div>

            <div v-if="product.variable_price" class="callout callout--warning product-card__note">
                This price is indicative ({{ formatMoney(product.price) }}) — the final price is filled in after pickup.
            </div>

            <div v-if="options.length" class="product-card__options">
                <p class="product-card__options-title">Choose your options</p>
                <div class="product-card__options-grid">
                    <button
                        v-for="option in options"
                        :key="option.id"
                        type="button"
                        class="chunk chunk--sm"
                        :class="option.selected ? 'chunk--teal' : 'chunk--cream'"
                        @click="toggleOption(option)"
                    >
                        {{ option.name }}
                    </button>
                </div>
            </div>

            <div class="product-card__comments">
                <label class="field-label">Comments</label>
                <textarea
                    v-model="comment"
                    class="field-textarea"
                    placeholder="Extra comment (e.g. without egg please)"
                    rows="2"
                ></textarea>
            </div>

            <template #actions>
                <button class="chunk chunk--teal" @click.stop="order">Place Order</button>
                <button class="chunk chunk--ghost" @click="showModal = false">Cancel</button>
            </template>
        </Modal>
    </div>
</div>
</template>

<style scoped>
@import "@css/pages/dashboard/sections/product-card.css";
</style>
