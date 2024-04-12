<template>
<div class="card-body flex justify-between align-middle">
    <div class="flex flex-end flex-row justify-between sm:flex sm:items-start">
        <div class="mt-3 sm:mt-0 ">
            <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
            <div class="mt-1 sm:flex sm:items-center ">
<!--                <p class="text-primary font-bold">{{ product.store.name }}</p>-->
            </div>
        </div>
        <div class="card-actions justify-end mt-2">
            <label
                :for="'option-modal-'+product.id"
                class="btn btn-sm btn-success modal-button">
                Order  <div class="badge bg-white text-success font-bold border-0 ml-3">{{ formatMoney(product.price) }}</div>
            </label>
            <input type="checkbox" :id="'option-modal-'+product.id" class="modal-toggle" />

            <label :for="'option-modal-'+product.id" class="modal modal-bottom sm:modal-middle cursor-pointer">
                <label class="modal-box relative" for="">
                    <h3 class="text-lg font-bold">{{product.name}}
                        <span class="ml-2 badge badge-warning badge-outline p-1">{{ formatMoney(product.price) }}</span>
                    </h3>
                    <div v-if="options.length >=0">
                        <p v-if="options.length" class="py-4">Choose your options</p>
                        <div class="flex flex-wrap gap-2">
                            <div v-for="option in options" class="flex">
                                <div class="btn btn-sm" :class="option.selected? 'btn-success':'btn-outline'" @click="toggleOption(option)">{{ option.name }}</div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <p>There are no options for this product</p>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button class="btn btn-success" @click="order">Place Order</button>
                    </div>
                </label>
            </label>
        </div>
    </div>

</div>
</template>

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
    methods:{
        formatMoney: helper.formatMoney,
        toggleOption(option){
            option.selected = !option.selected;

        },
        order(){
            this.$emit('ordered',this.product);
            this.closeModal();
        },
        closeModal(){
            document.getElementById('option-modal-'+this.product.id).checked = false;
        }
    },
    computed:{
        options(){
            return this.product.options;
        }
    }
}
</script>

<style scoped>

</style>
