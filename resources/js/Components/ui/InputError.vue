<script>
export default {
    props: ['message']
}
</script>
<template>
    <div v-show="message">
        <p class="field-error">
            {{ message }}
        </p>
    </div>
</template>
