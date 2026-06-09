<script>
import {useToast} from "vue-toastification";
const toast = useToast();
export default {
    watch: {
        '$page.props.flash': {
            handler($response) {
                if ($response.success) {
                    toast.success($response.success);
                }
            },
            deep: true,
        },
    },
}
</script>
<template></template>
