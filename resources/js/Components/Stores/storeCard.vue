<template>
<div class="card-body flex justify-between align-middle">
    <div class="flex flex-end flex-row justify-between sm:flex sm:items-start">
        <div class="mt-3 sm:mt-0 ">
            <div class="text-sm font-medium text-gray-900">{{ store.name }}</div>
            <div class="mt-1 sm:flex sm:items-center ">
<!--                <p class="text-primary font-bold">{{ product.store.name }}</p>-->
            </div>
        </div>
        <div class="card-actions justify-end mt-2">
            <label
                @click="$emit('select-store', store)"
                :for="'option-modal-'+store.id"
                class="btn btn-sm btn-success modal-button">
                Products  <div class="badge bg-white text-success font-bold border-0 ml-3">{{store.products_count}}</div>
            </label>
        </div>
    </div>

</div>
</template>

<script>
import Checkbox from "@/Components/Checkbox.vue";
export default {
    name: "productCard",
    components : {
      Checkbox,
    },
    mounted() {
        console.log(this.store)
    },
    props: {
        store : Object,
    },
    methods:{
    },
    computed:{
    }
}
</script>

<style scoped>

</style>
