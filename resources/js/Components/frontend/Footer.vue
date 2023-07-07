<template>
    <footer class="footer p-10 bg-transparent/20 text-white">
        <div>
            <img src="/images/logo_w.png" class="w-48" alt="">
        </div>
        <div class="">
            <ul v-if="!$page.props.auth.user">
                <li><p><a :href="route('home')">Home</a></p></li>
                <li v-if="$page.props.auth.user"><p><a :href="route('dashboard')" class="">Dashboard</a></p></li>
                <li v-else><p><a :href="route('login')" class="">Login</a></p></li>
                <li><p><a :href="route('company.register')" class="">Get started</a></p></li>
            </ul>
        </div>
        <div class="">
            <ul>
                <li><p><a :href="route('legal.general')">General</a></p></li>
                <li><p><a :href="route('legal.privacy')">Privacy</a></p></li>
                <li><p><a :href="route('legal.cookies')">Cookies</a></p></li>
            </ul>
        </div>
        <div>
            <ul>
                <li><p>Powered by </p></li>
                <li><p><a target="_blank" href="https://libaro.be">Libaro bv</a></p></li>
            </ul>
        </div>
    </footer>
</template>

<script>
export default {
    name: "Footer"
}
</script>

<style scoped>
a{
    @apply text-white font-bold hover:text-info;
}
</style>
