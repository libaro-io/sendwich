<script>
export default {
    props: ['modelValue'],

    emits: ['update:modelValue'],

    methods: {
        focus() {
            this.$refs.input.focus()
        }
    }
}
</script>
<template>
    <input class="border-gray-300 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200/50 rounded-md shadow-xs" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input">
</template>
