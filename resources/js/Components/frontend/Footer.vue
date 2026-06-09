<script>
export default {
    name: "Footer"
}
</script>
<template>
    <footer class="site-footer">
        <div>
            <img src="/images/logo_w.png" class="site-footer__logo" alt="Sendwich Logo">
        </div>
        <div class="site-footer__cols">
            <div>
                <ul v-if="!$page.props.auth.user" class="site-footer__list">
                    <li><a :href="route('home')">Home</a></li>
                    <li v-if="$page.props.auth.user"><a :href="route('dashboard')">Dashboard</a></li>
                    <li v-else><a :href="route('login')">Login</a></li>
                    <li><a :href="route('company.register')">Get started</a></li>
                </ul>
            </div>
            <div>
                <ul class="site-footer__list">
                    <li><a :href="route('legal.general')">General</a></li>
                    <li><a :href="route('legal.privacy')">Privacy</a></li>
                    <li><a :href="route('legal.cookies')">Cookies</a></li>
                </ul>
            </div>
            <div>
                <ul class="site-footer__list site-footer__list--muted">
                    <li><p class="site-footer__label">Powered by</p></li>
                    <li><a target="_blank" href="https://libaro.be">Libaro bv</a></li>
                </ul>
            </div>
        </div>
    </footer>
</template>

<style scoped>
@import "@css/components/frontend/footer.css";
</style>