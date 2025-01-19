<template>

</template>

<script>
export default {
    name: "Stripe",
    mounted: function () {
        let app = this
        Vue.loadScript("https://js.stripe.com/v3/").then(function () {
            if (app.$route.query.publishableKey && app.$route.query.sessionId) {
                app.redirectToCheckout()
            }
        })

    },
    computed: {
        publishableKey() {
            return this.$route.query.publishableKey
        },
        sessionId() {
            return this.$route.query.sessionId
        }
    },
    methods: {
        redirectToCheckout() {
            Stripe(this.publishableKey).redirectToCheckout({
                sessionId: this.sessionId,
            })
        }
    }
}
</script>

<style scoped>

</style>
