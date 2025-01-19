<template>
    <button
        type="button"
        class="AcceptUI btn btn-success"
        data-billingAddressOptions='{"show":true, "required":true}'
        :data-apiLoginID="loginId"
        :data-clientKey="clientKey"
        data-acceptUIFormBtnTxt="Submit"
        data-acceptUIFormHeaderTxt="Card Information"
        data-responseHandler="checkAuthorize"
    >
        {{ translate('orders.processpayment') }}
    </button>
</template>

<script>
export default {
    name: "Authorize",
    props: {
        loginId: {
            type: String,
            required: true
        },
        clientKey: {
            type: String,
            required: true
        }
    },
    created() {
        const src = 'https://jstest.authorize.net/v3/AcceptUI.js';
        const el = document.querySelector('script[src="' + src + '"]');

        if (el) {
            Vue.unloadScript(src)
        }

        Vue.loadScript(src)
        window.checkAuthorize = this.checkAuthorize;

    },
    methods: {
        checkAuthorize(data) {
            if (data.messages.resultCode === 'Ok') {
                this.$emit('payment-success', data.opaqueData)
            }
        }
    }
}
</script>

<style scoped>

</style>
