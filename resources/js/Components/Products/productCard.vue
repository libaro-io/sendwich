<template>
<div class="flex justify-between bg-gray-50 py-4 my-4 rounded rounded-md align-middle">
    <div class="sm:flex sm:items-start">
        <div class="mt-3 sm:mt-0 sm:ml-4">
            <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
            <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center">
                <div><span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">€ {{
                        product.price
                    }}</span></div>
                <span class="hidden sm:mx-2 sm:inline"
                      aria-hidden="true"> &middot; </span>
                <div class="mt-1 sm:mt-0">{{ deliveryMoment }}</div>
            </div>
        </div>
    </div>
    <div class="flex flex-col lg:flex-row justify-end items-center">
        <button
            class="btn btn-sm btn-success mr-4"
            @click="addProduct(product)"
        >
            Bestel
        </button>
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
    props: {
        product : Object,
    }
}
</script>

<style scoped>

</style>
