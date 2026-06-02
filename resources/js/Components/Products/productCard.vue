<script>
import Checkbox from "@/Components/Checkbox.vue";
import {useHelpers} from "@/Composables/helpers";

const helper = useHelpers();
export default {
    name: "productCard",
    components : {
      Checkbox,
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
            this.closeModal();
        },
        closeModal(){
            document.getElementById('option-modal-'+this.product.id).checked = false;
        }
    },
}
</script>
<template>
<div class="card-body flex justify-between align-middle">
    <div class="flex flex-end flex-row justify-between sm:flex sm:items-start">
        <div class="mt-3 sm:mt-0 ">
            <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
            <div class="mt-1">
                <span v-if="product.variable_price" class="text-xs text-gray-400">Price per kg</span>
            </div>
        </div>
        <div class="card-actions justify-end mt-2">
            <label
                :for="'option-modal-'+product.id"
                class="btn btn-sm btn-success modal-button">
                Order
                <div class="badge bg-white text-success font-bold border-0 ml-3">
                    {{ formatMoney(product.price) }}{{ product.variable_price ? '/kg' : '' }}
                </div>
            </label>
            <input type="checkbox" :id="'option-modal-'+product.id" class="modal-toggle" />

            <label :for="'option-modal-'+product.id" class="modal modal-bottom sm:modal-middle cursor-pointer">
                <label class="modal-box relative" for="">
                    <h3 class="text-lg font-bold">{{product.name}}
                        <span class="ml-2 badge badge-warning badge-outline p-1">
                            {{ product.variable_price ? formatMoney(product.price) + '/kg' : formatMoney(product.price) }}
                        </span>
                    </h3>
                    <div v-if="product.variable_price" class="mt-3 p-3 bg-amber-50 rounded-lg text-sm text-amber-700">
                        The price is determined by the runner based on the weight ({{ formatMoney(product.price) }}/kg).
                    </div>
                    <div v-if="options.length >=0">
                        <p v-if="options.length" class="py-4">Choose your options</p>
                        <div class="flex flex-wrap gap-2">
                            <div v-for="option in options" class="flex">
                                <div class="btn btn-sm" :class="option.selected? 'btn-success':'btn-outline'" @click="toggleOption(option)">{{ option.name }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <textarea
                            v-model="comment"
                            class="textarea textarea-bordered w-full"
                            placeholder="Extra comment (e.g. without egg please)"
                            rows="1"
                        ></textarea>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button class="btn btn-success" @click.stop="order">Place Order</button>
                    </div>
                </label>
            </label>
        </div>
    </div>

</div>
</template>
<style scoped>

</style>
