<template>
    <div>
        <authorize
            v-if="payment.method === 'authorize'"
            @payment-success="setExtraData"
            :client-key="payment.settings.public_client_key"
            :login-id="payment.settings.login_id"
        >
        </authorize>

        <button v-else type="button" class="btn btn-success" @click.prevent="processPayment()">
            {{ translate('orders.processpayment') }}
        </button>
    </div>
</template>

<script>
import api from "@/api";
import Authorize from "@component/billingType/Authorize";

export default {
    props: {
        payment: Object,
        order: Object,
        billingType: {
            type: String,
            default: 'regular'
        }
    },
    components: {
        Authorize,
    },
    mounted: function () {
        Vue.loadScript("https://js.stripe.com/v3/")
    },
    data: function () {
        return {
            extraData: {},
            is_loader: false,
        };
    },
    methods: {
        setExtraData(data) {
            this.extraData.authorize = data;
            this.$root.loading = true;
            setTimeout(this.processPayment, 5000);
        },

        downloadFile(fileUrl) {
            axios({
                url: fileUrl,
                method: 'GET',
                responseType: 'blob',
            }).then((response) => {
                var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                var fileLink = document.createElement('a');
                fileLink.href = fileURL;
                fileLink.setAttribute('download', 'file.pdf');
                document.body.appendChild(fileLink);
                fileLink.click();
            });
        },
        processPayment() {
            if (this.billingType === 'subscription') {
                return this.subscribe();
            } else if (this.billingType === 'installment') {
                return this.installment();
            } else {
                this.regular()
            }
        },

        regular() {
            let params = {
                order_id: this.order.id,
                payment_method_id: this.payment.id,
                payment_type: this.payment.method,
                billing_type: 'regular'
            }

            this.paymentInvoice(params);
        },

        installment() {

            let params = {
                order_id: this.order.id,
                payment_method_id: this.payment.id,
                payment_type: this.payment.method,
                billing_type: 'installment'
            }

            this.paymentInvoice(params);
        },
        newWindow(href) {
            let x = screen.width / 2 - 700 / 2;
            let y = screen.height / 2 - 450 / 2;
            window.open(href, 'sharegplus', 'height=485,width=700,left=' + x + ',top=' + y);
        },

        paymentInvoice(params) {
            let app = this;

            let returnURL = app.$router.resolve({
                name: 'Close', query: {
                    close: true
                }
            });

            params.extra_data = this.extraData;
            params.extra_data.urls = {
                return_url: window.location.origin + returnURL.href,
                cancel_url: window.location.origin + returnURL.href
            }

            app.$root.loading = true;

            axios.post('/orders/payment-invoice', params)
                .then(function (resp) {
                    if (resp.data.status) {
                        switch (resp.data.options.payment_type) {
                            case 'paypal':
                                app.newWindow(resp.data.options.paypal.approve)
                                break;
                            case 'stripe':
                                let routeData = app.$router.resolve({
                                    name: 'Stripe', query: {
                                        sessionId: resp.data.options.stripe.sessionId,
                                        publishableKey: resp.data.options.stripe.publishableKey
                                    }
                                });
                                app.newWindow(routeData.href)
                                break;
                            case 'authorize':
                                app.authorizeUrl = resp.data.options.authorize.url
                                break;
                            default:
                                if (resp.data.options.invoicePath) {
                                    app.downloadFile(resp.data.options.invoicePath)
                                }
                        }
                        app.$emit('order-completed', resp.data.options.order_id)
                        app.$root.loading = false;
                    }
                })
                .catch(function () {
                    console.log("error get orders");
                    app.$root.loading = false;
                });
        },

        subscribe() {
            let app = this;
            let params = this.order;

            params.extra_data = this.extraData;
            let returnURL = app.$router.resolve({
                name: 'Close', query: {
                    close: true
                }
            });

            params.extra_data = this.extraData;
            params.extra_data.urls = {
                return_url: window.location.origin + returnURL.href,
                cancel_url: window.location.origin + returnURL.href
            }
            params.payment_method_id = this.payment.id
            params.payment_type = this.payment.method
            params.billing_type = 'subscription'

            app.$root.loading = true;

            axios.post(api.urls.subscribe.subscribe, this.order)
                .then(function (resp) {

                    if (resp.data.status) {
                        switch (resp.data.options.payment_type) {
                            case 'paypal':
                                app.newWindow(resp.data.options.paypal.approve)
                                break
                            case 'stripe':
                                let routeData = app.$router.resolve({
                                    name: 'Stripe', query: {
                                        sessionId: resp.data.options.stripe.sessionId,
                                        publishableKey: resp.data.options.stripe.publishableKey
                                    }
                                });
                                app.newWindow(routeData.href)

                                break;
                            default:
                                if (resp.data.options.invoicePath) {
                                    app.downloadFile(resp.data.options.invoicePath)
                                }
                        }
                        app.$emit('order-completed', resp.data.options.order_id)
                    }
                    app.$root.loading = false;
                })
                .catch(function (err) {
                    console.log(err);
                    app.$root.loading = false;
                });
        }
    }
}
</script>
